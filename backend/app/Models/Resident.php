<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Resident extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'foto_ktp',
        'status',
        'nomor_hp',
        'status_menikah',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pekerjaan',
        'tanggal_masuk',
        'catatan',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = ['foto_ktp_url'];

    public function getFotoKtpUrlAttribute(): ?string
    {
        if ($this->foto_ktp) {
            return Storage::url($this->foto_ktp);
        }
        return null;
    }

    public function houseHistories(): HasMany
    {
        return $this->hasMany(ResidentHouseHistory::class);
    }

    public function currentHouse(): HasOne
    {
        return $this->hasOne(House::class, 'current_resident_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTetap($query)
    {
        return $query->where('status', 'tetap');
    }

    public function scopeKontrak($query)
    {
        return $query->where('status', 'kontrak');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nama_lengkap', 'like', "%{$term}%")
              ->orWhere('nik', 'like', "%{$term}%")
              ->orWhere('nomor_hp', 'like', "%{$term}%");
        });
    }
}