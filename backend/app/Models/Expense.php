<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_category_id',
        'nama_pengeluaran',
        'nominal',
        'tanggal',
        'keterangan',
        'bukti_nota',
        'created_by',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal' => 'date',
    ];

    protected $appends = ['bukti_nota_url'];

    public function getBuktiNotaUrlAttribute(): ?string
    {
        if ($this->bukti_nota) {
            return Storage::url($this->bukti_nota);
        }
        return null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nama_pengeluaran', 'like', "%{$term}%")
              ->orWhere('keterangan', 'like', "%{$term}%");
        });
    }

    public function scopeByPeriod($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }
}