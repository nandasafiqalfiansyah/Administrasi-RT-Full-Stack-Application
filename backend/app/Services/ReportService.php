<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Expense;
use App\Models\MonthlyBill;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getMonthlySummary(int $bulan, int $tahun): array
    {
        $totalPemasukan = Payment::whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->sum('nominal');

        $totalPengeluaran = Expense::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('nominal');

        $totalTagihan = MonthlyBill::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->sum('nominal');

        $totalLunas = MonthlyBill::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'lunas')
            ->sum('nominal');

        return [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_pemasukan' => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'saldo' => $totalPemasukan - $totalPengeluaran,
            'total_tagihan' => $totalTagihan,
            'total_lunas' => $totalLunas,
            'persentase_lunas' => $totalTagihan > 0 ? round(($totalLunas / $totalTagihan) * 100, 2) : 0,
        ];
    }

    public function getYearlyChart(int $tahun): array
    {
        $pemasukan = [];
        $pengeluaran = [];

        for ($i = 1; $i <= 12; $i++) {
            $pemasukan[] = (float) Payment::whereMonth('tanggal_bayar', $i)
                ->whereYear('tanggal_bayar', $tahun)
                ->sum('nominal');

            $pengeluaran[] = (float) Expense::whereMonth('tanggal', $i)
                ->whereYear('tanggal', $tahun)
                ->sum('nominal');
        }

        return [
            'tahun' => $tahun,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
        ];
    }

    public function getDetailReport(int $bulan, int $tahun): array
    {
        $pemasukan = Payment::with(['house.currentResident', 'paymentType', 'createdBy'])
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $pengeluaran = Expense::with(['category', 'createdBy'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        return [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'summary' => $this->getMonthlySummary($bulan, $tahun),
        ];
    }

    public function exportPdf(int $bulan, int $tahun)
    {
        // PDF export logic - can be implemented with barryvdh/laravel-dompdf
        $data = $this->getDetailReport($bulan, $tahun);
        // Return PDF view
        return $data;
    }

    public function exportExcel(int $bulan, int $tahun)
    {
        // Excel export logic - can be implemented with maatwebsite/laravel-excel
        $data = $this->getDetailReport($bulan, $tahun);
        // Return Excel export
        return $data;
    }
}