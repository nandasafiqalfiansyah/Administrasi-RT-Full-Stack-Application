<?php

namespace App\Services;

use App\Models\House;
use App\Models\ResidentHouseHistory;
use App\Repositories\Contracts\HouseRepositoryInterface;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class HouseService
{
    public function __construct(
        private HouseRepositoryInterface $houseRepository
    ) {}

    public function getAll(array $filters = [])
    {
        $query = House::with(['currentResident', 'houseHistories.resident']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'nomor_rumah';
        $sortDir = $filters['sort_direction'] ?? 'asc';
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }

    public function find(int $id): House
    {
        return House::with(['currentResident', 'houseHistories.resident'])->findOrFail($id);
    }

    public function create(array $data): House
    {
        return DB::transaction(function () use ($data) {
            $house = $this->houseRepository->create($data);

            if ($house->status === 'dihuni' && isset($data['current_resident_id'])) {
                ResidentHouseHistory::create([
                    'house_id' => $house->id,
                    'resident_id' => $data['current_resident_id'],
                    'tanggal_masuk' => now(),
                    'status' => 'tetap',
                ]);
            }

            ActivityLog::log('create', 'houses', "Menambahkan rumah {$house->nomor_rumah}", $house);

            return $house;
        });
    }

    public function update(int $id, array $data): House
    {
        return DB::transaction(function () use ($id, $data) {
            $house = $this->houseRepository->findOrFail($id);
            $oldData = $house->toArray();

            $oldResidentId = $house->current_resident_id;
            $newResidentId = $data['current_resident_id'] ?? null;

            $house = $this->houseRepository->update($house, $data);

            if ($oldResidentId !== $newResidentId) {
                if ($oldResidentId) {
                    ResidentHouseHistory::where('house_id', $house->id)
                        ->where('resident_id', $oldResidentId)
                        ->whereNull('tanggal_keluar')
                        ->update(['tanggal_keluar' => now()]);
                }

                if ($newResidentId) {
                    ResidentHouseHistory::create([
                        'house_id' => $house->id,
                        'resident_id' => $newResidentId,
                        'tanggal_masuk' => now(),
                        'status' => $data['status_penghuni'] ?? 'tetap',
                    ]);
                }
            }

            ActivityLog::log('update', 'houses', "Mengupdate rumah {$house->nomor_rumah}", $house, $oldData, $house->toArray());

            return $house;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $house = $this->houseRepository->findOrFail($id);
            ActivityLog::log('delete', 'houses', "Menghapus rumah {$house->nomor_rumah}", $house);
            return $this->houseRepository->delete($house);
        });
    }

    public function getHistory(int $houseId)
    {
        return ResidentHouseHistory::with('resident')
            ->where('house_id', $houseId)
            ->orderBy('tanggal_masuk', 'desc')
            ->get();
    }
}