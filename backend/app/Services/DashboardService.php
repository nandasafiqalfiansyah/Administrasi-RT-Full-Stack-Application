<?php

namespace App\Services;

use App\Repositories\Contracts\ResidentRepositoryInterface;
use App\Repositories\Contracts\HouseRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;

class DashboardService
{
    public function __construct(
        private ResidentRepositoryInterface $residentRepository,
        private HouseRepositoryInterface $houseRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private ExpenseRepositoryInterface $expenseRepository,
    ) {}

    public function getOverview(): array
    {
        $tahun = now()->year;

        $totalPemasukan = $this->paymentRepository->getTotalPemasukanBulanIni();
        $totalPengeluaran = $this->expenseRepository->getTotalPengeluaranBulanIni();

        return [
            'total_rumah' => $this->houseRepository->getTotalRumah(),
            'rumah_dihuni' => $this->houseRepository->getDihuniCount(),
            'rumah_kosong' => $this->houseRepository->getTidakDihuniCount(),
            'total_penghuni' => $this->residentRepository->getTotalActive(),
            'penghuni_tetap' => $this->residentRepository->getTetapCount(),
            'penghuni_kontrak' => $this->residentRepository->getKontrakCount(),
            'total_pemasukan_bulan_ini' => $totalPemasukan,
            'total_pengeluaran_bulan_ini' => $totalPengeluaran,
            'saldo' => $totalPemasukan - $totalPengeluaran,
            'grafik_pemasukan_pengeluaran' => [
                'pemasukan' => $this->paymentRepository->getPemasukanPerBulan($tahun),
                'pengeluaran' => $this->expenseRepository->getPengeluaranPerBulan($tahun),
                'tahun' => $tahun,
            ],
            'grafik_pembayaran_iuran' => $this->paymentRepository->getPembayaranIuranPerBulan($tahun),
        ];
    }
}