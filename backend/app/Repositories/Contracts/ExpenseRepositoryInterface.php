<?php

namespace App\Repositories\Contracts;

interface ExpenseRepositoryInterface extends BaseRepositoryInterface
{
    public function getTotalPengeluaranBulanIni(): float;
    public function getTotalPengeluaranByPeriod(int $bulan, int $tahun): float;
    public function getPengeluaranPerBulan(int $tahun): array;
}