<?php

namespace App\Services;

use App\Models\Resident;
use App\Repositories\Contracts\ResidentRepositoryInterface;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResidentService
{
    public function __construct(
        private ResidentRepositoryInterface $residentRepository
    ) {}

    public function getAll(array $filters = [])
    {
        $query = \App\Models\Resident::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === 'true' || $filters['is_active'] === true);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }

    public function find(int $id): Resident
    {
        return $this->residentRepository->findOrFail($id);
    }

    public function create(array $data): Resident
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['foto_ktp']) && $data['foto_ktp'] instanceof \Illuminate\Http\UploadedFile) {
                $data['foto_ktp'] = $data['foto_ktp']->store('foto-ktp', 'public');
            }

            $resident = $this->residentRepository->create($data);

            ActivityLog::log('create', 'residents', "Menambahkan penghuni {$resident->nama_lengkap}", $resident);

            return $resident;
        });
    }

    public function update(int $id, array $data): Resident
    {
        return DB::transaction(function () use ($id, $data) {
            $resident = $this->residentRepository->findOrFail($id);
            $oldData = $resident->toArray();

            if (isset($data['foto_ktp']) && $data['foto_ktp'] instanceof \Illuminate\Http\UploadedFile) {
                if ($resident->foto_ktp) {
                    Storage::disk('public')->delete($resident->foto_ktp);
                }
                $data['foto_ktp'] = $data['foto_ktp']->store('foto-ktp', 'public');
            }

            $resident = $this->residentRepository->update($resident, $data);

            ActivityLog::log('update', 'residents', "Mengupdate penghuni {$resident->nama_lengkap}", $resident, $oldData, $resident->toArray());

            return $resident;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $resident = $this->residentRepository->findOrFail($id);

            if ($resident->foto_ktp) {
                Storage::disk('public')->delete($resident->foto_ktp);
            }

            ActivityLog::log('delete', 'residents', "Menghapus penghuni {$resident->nama_lengkap}", $resident);

            return $this->residentRepository->delete($resident);
        });
    }
}