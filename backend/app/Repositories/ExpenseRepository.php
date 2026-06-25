<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ExpenseRepository extends BaseRepository implements ExpenseRepositoryInterface
{
    public function __construct(Expense $model)
    {
        parent::__construct($model);
    }

    public function getTotalPengeluaranBulanIni(): float
    {
        return $this->model->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('nominal');
    }

    public function getTotalPengeluaranByPeriod(int $bulan, int $tahun): float
    {
        return $this->model->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('nominal');
    }

    public function getPengeluaranPerBulan(int $tahun): array
    {
        $data = $this->model->select(
            DB::raw('MONTH(tanggal) as bulan'),
            DB::raw('SUM(nominal) as total')
        )
        ->whereYear('tanggal', $tahun)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->keyBy('bulan');

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = (float) ($data[$i]->total ?? 0);
        }
        return $result;
    }
}