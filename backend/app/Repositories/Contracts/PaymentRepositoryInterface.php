<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getTotalPemasukanBulanIni(): float;
    public function getTotalPemasukanByPeriod(int $bulan, int $tahun): float;
    public function getPemasukanPerBulan(int $tahun): array;
    public function getPembayaranIuranPerBulan(int $tahun): array;
}