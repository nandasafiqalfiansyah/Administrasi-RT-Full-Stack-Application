<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class House extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomor_rumah',
        'blok',
        'status',
        'current_resident_id',
        'catatan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function currentResident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'current_resident_id');
    }

    public function houseHistories(): HasMany
    {
        return $this->hasMany(ResidentHouseHistory::class)->latest('tanggal_masuk');
    }

    public function monthlyBills(): HasMany
    {
        return $this->hasMany(MonthlyBill::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeDihuni($query)
    {
        return $query->where('status', 'dihuni');
    }

    public function scopeTidakDihuni($query)
    {
        return $query->where('status', 'tidak_dihuni');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nomor_rumah', 'like', "%{$term}%")
              ->orWhere('blok', 'like', "%{$term}%");
        });
    }
}