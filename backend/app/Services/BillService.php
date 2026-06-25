<?php

namespace App\Services;

use App\Models\House;
use App\Models\MonthlyBill;
use App\Models\PaymentType;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class BillService
{
    /**
     * Generate monthly bills automatically for all occupied houses
     */
    public function generateMonthlyBills(?int $bulan = null, ?int $tahun = null): int
    {
        $bulan = $bulan ?? now()->month;
        $tahun = $tahun ?? now()->year;

        $paymentTypes = PaymentType::active()->get();
        $houses = House::dihuni()->get();

        $count = 0;

        DB::transaction(function () use ($houses, $paymentTypes, $bulan, $tahun, &$count) {
            foreach ($houses as $house) {
                foreach ($paymentTypes as $paymentType) {
                    $exists = MonthlyBill::where('house_id', $house->id)
                        ->where('payment_type_id', $paymentType->id)
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->exists();

                    if (!$exists) {
                        MonthlyBill::create([
                            'house_id' => $house->id,
                            'payment_type_id' => $paymentType->id,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                            'nominal' => $paymentType->nominal,
                            'status' => 'belum_lunas',
                            'jatuh_tempo' => now()->day(10),
                        ]);
                        $count++;
                    }
                }
            }
        });

        ActivityLog::log('generate', 'monthly_bills', "Generate tagihan bulan {$bulan}/{$tahun} untuk {$count} item", null);

        return $count;
    }

    public function getBills(array $filters = [])
    {
        $query = MonthlyBill::with(['house.currentResident', 'paymentType']);

        if (!empty($filters['house_id'])) {
            $query->where('house_id', $filters['house_id']);
        }

        if (!empty($filters['payment_type_id'])) {
            $query->where('payment_type_id', $filters['payment_type_id']);
        }

        if (!empty($filters['bulan'])) {
            $query->where('bulan', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('house_id')
            ->paginate($perPage);
    }

    public function getBillsSummary(?int $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? now()->month;
        $tahun = $tahun ?? now()->year;

        $totalTagihan = MonthlyBill::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->sum('nominal');

        $totalLunas = MonthlyBill::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'lunas')
            ->sum('nominal');

        $totalBelumLunas = MonthlyBill::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'belum_lunas')
            ->sum('nominal');

        return [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_tagihan' => $totalTagihan,
            'total_lunas' => $totalLunas,
            'total_belum_lunas' => $totalBelumLunas,
            'persentase_lunas' => $totalTagihan > 0 ? round(($totalLunas / $totalTagihan) * 100, 2) : 0,
        ];
    }
}