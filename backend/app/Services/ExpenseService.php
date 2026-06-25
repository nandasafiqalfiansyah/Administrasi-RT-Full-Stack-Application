<?php

namespace App\Services;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
    public function __construct(
        private ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function getAll(array $filters = [])
    {
        $query = Expense::with(['category', 'createdBy']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['expense_category_id'])) {
            $query->where('expense_category_id', $filters['expense_category_id']);
        }

        if (!empty($filters['bulan'])) {
            $query->whereMonth('tanggal', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $query->whereYear('tanggal', $filters['tahun']);
        }

        $sortBy = $filters['sort_by'] ?? 'tanggal';
        $sortDir = $filters['sort_direction'] ?? 'desc';
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }

    public function find(int $id): Expense
    {
        return $this->expenseRepository->findOrFail($id);
    }

    public function create(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();

            if (isset($data['bukti_nota']) && $data['bukti_nota'] instanceof \Illuminate\Http\UploadedFile) {
                $data['bukti_nota'] = $data['bukti_nota']->store('nota', 'public');
            }

            $expense = $this->expenseRepository->create($data);

            ActivityLog::log('create', 'expenses', "Pengeluaran {$expense->nama_pengeluaran} - Rp " . number_format($expense->nominal, 0), $expense);

            return $expense;
        });
    }

    public function update(int $id, array $data): Expense
    {
        return DB::transaction(function () use ($id, $data) {
            $expense = $this->expenseRepository->findOrFail($id);
            $oldData = $expense->toArray();

            if (isset($data['bukti_nota']) && $data['bukti_nota'] instanceof \Illuminate\Http\UploadedFile) {
                if ($expense->bukti_nota) {
                    Storage::disk('public')->delete($expense->bukti_nota);
                }
                $data['bukti_nota'] = $data['bukti_nota']->store('nota', 'public');
            }

            $expense = $this->expenseRepository->update($expense, $data);

            ActivityLog::log('update', 'expenses', "Mengupdate pengeluaran {$expense->nama_pengeluaran}", $expense, $oldData, $expense->toArray());

            return $expense;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $expense = $this->expenseRepository->findOrFail($id);
            ActivityLog::log('delete', 'expenses', "Menghapus pengeluaran {$expense->nama_pengeluaran}", $expense);
            return $this->expenseRepository->delete($expense);
        });
    }
}