<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Models\MonthlyBill;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function getTotalPemasukanBulanIni(): float
    {
        return $this->model->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('nominal');
    }

    public function getTotalPemasukanByPeriod(int $bulan, int $tahun): float
    {
        return $this->model->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->sum('nominal');
    }

    public function getPemasukanPerBulan(int $tahun): array
    {
        $data = $this->model->select(
            DB::raw('CAST(strftime("%m", tanggal_bayar) as INTEGER) as bulan'),
            DB::raw('SUM(nominal) as total')
        )
        ->whereYear('tanggal_bayar', $tahun)
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

    public function getPembayaranIuranPerBulan(int $tahun): array
    {
        $data = MonthlyBill::select(
            DB::raw('CAST(strftime("%m", jatuh_tempo) as INTEGER) as bulan'),
            DB::raw('SUM(CASE WHEN status = "lunas" THEN nominal ELSE 0 END) as total_lunas'),
            DB::raw('SUM(nominal) as total_tagihan')
        )
        ->whereYear('jatuh_tempo', $tahun)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->keyBy('bulan');

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = [
                'bulan' => $i,
                'total_tagihan' => (float) ($data[$i]->total_tagihan ?? 0),
                'total_lunas' => (float) ($data[$i]->total_lunas ?? 0),
            ];
        }
        return $result;
    }
}