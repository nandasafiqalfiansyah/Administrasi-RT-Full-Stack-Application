<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\MonthlyBill;
use App\Models\PaymentType;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository
    ) {}

    public function getAll(array $filters = [])
    {
        $query = Payment::with(['house.currentResident', 'resident', 'paymentType', 'createdBy']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('kode_pembayaran', 'like', "%{$filters['search']}%")
                  ->orWhere('keterangan', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['house_id'])) {
            $query->where('house_id', $filters['house_id']);
        }

        if (!empty($filters['payment_type_id'])) {
            $query->where('payment_type_id', $filters['payment_type_id']);
        }

        if (!empty($filters['bulan'])) {
            $query->whereMonth('tanggal_bayar', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $query->whereYear('tanggal_bayar', $filters['tahun']);
        }

        $sortBy = $filters['sort_by'] ?? 'tanggal_bayar';
        $sortDir = $filters['sort_direction'] ?? 'desc';
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }

    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            // Generate kode pembayaran
            $data['kode_pembayaran'] = Payment::generateKode();
            $data['created_by'] = auth()->id();

            // Handle payment for multiple months or full year
            $bulanList = [];
            if (isset($data['bulan_mulai']) && isset($data['bulan_selesai'])) {
                $mulai = (int) $data['bulan_mulai'];
                $selesai = (int) $data['bulan_selesai'];
                for ($b = $mulai; $b <= $selesai; $b++) {
                    $bulanList[] = $b;
                }
            } elseif (isset($data['bulan'])) {
                $bulanList[] = (int) $data['bulan'];
            }

            $paymentType = PaymentType::findOrFail($data['payment_type_id']);
            $totalNominal = $paymentType->nominal * count($bulanList);

            // Create payment record
            $payment = $this->paymentRepository->create([
                'kode_pembayaran' => $data['kode_pembayaran'],
                'house_id' => $data['house_id'],
                'resident_id' => $data['resident_id'] ?? null,
                'payment_type_id' => $data['payment_type_id'],
                'monthly_bill_id' => $data['monthly_bill_id'] ?? null,
                'nominal' => $totalNominal,
                'tanggal_bayar' => $data['tanggal_bayar'] ?? now(),
                'metode_pembayaran' => $data['metode_pembayaran'] ?? 'tunai',
                'bukti_bayar' => $data['bukti_bayar'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'created_by' => $data['created_by'],
            ]);

            // Update monthly bills as paid
            foreach ($bulanList as $bulan) {
                $tahun = $data['tahun'] ?? now()->year;
                $bill = MonthlyBill::where('house_id', $data['house_id'])
                    ->where('payment_type_id', $data['payment_type_id'])
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();

                if ($bill) {
                    $bill->update([
                        'status' => 'lunas',
                        'tanggal_lunas' => now(),
                    ]);
                }
            }

            ActivityLog::log('create', 'payments', "Pembayaran {$payment->kode_pembayaran} - Rp " . number_format($totalNominal, 0), $payment);

            return $payment;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $payment = $this->paymentRepository->findOrFail($id);
            ActivityLog::log('delete', 'payments', "Menghapus pembayaran {$payment->kode_pembayaran}", $payment);
            return $this->paymentRepository->delete($payment);
        });
    }
}