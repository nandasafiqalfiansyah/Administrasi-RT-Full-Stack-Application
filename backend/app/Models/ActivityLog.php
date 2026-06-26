<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Membuat',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            'login' => 'Masuk',
            'logout' => 'Keluar',
            'seed' => 'Seed Data',
            default => ucfirst($this->action),
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'create' => 'emerald',
            'update' => 'blue',
            'delete' => 'rose',
            'login' => 'amber',
            'logout' => 'neutral',
            'seed' => 'purple',
            default => 'neutral',
        };
    }

    public function getModelLabelAttribute(): string
    {
        $modelType = $this->subject_type;
        
        switch ($modelType) {
            case 'App\\Models\\Resident':
                return 'Penghuni';
            case 'App\\Models\\House':
                return 'Rumah';
            case 'App\\Models\\Payment':
                return 'Pembayaran';
            case 'App\\Models\\Expense':
                return 'Pengeluaran';
            case 'App\\Models\\MonthlyBill':
                return 'Tagihan';
            default:
                return $modelType ? class_basename($modelType) : '-';
        }
    }

    public static function log(string $action, string $module, string $description, $subject = null, array $oldData = null, array $newData = null): self
    {
        $user = auth()->user();
        
        $data = [
            'user_id' => $user?->id,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        return static::create($data);
    }
}
