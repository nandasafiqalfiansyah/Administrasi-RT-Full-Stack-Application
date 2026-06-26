<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_pembayaran',
        'house_id',
        'resident_id',
        'payment_type_id',
        'monthly_bill_id',
        'nominal',
        'tanggal_bayar',
        'metode_pembayaran',
        'bukti_bayar',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    protected $appends = ['bukti_bayar_url'];

    public function getBuktiBayarUrlAttribute(): ?string
    {
        if ($this->bukti_bayar) {
            return Storage::url($this->bukti_bayar);
        }
        return null;
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function monthlyBill(): BelongsTo
    {
        return $this->belongsTo(MonthlyBill::class, 'monthly_bill_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateKode(): string
    {
        $prefix = 'PMT';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid('', true), -6));
        return "{$prefix}-{$date}-{$random}";
    }
}