<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_pengeluaran' => $this->nama_pengeluaran,
            'category' => [
                'id' => $this->category?->id,
                'nama' => $this->category?->nama,
            ],
            'nominal' => (float) $this->nominal,
            'nominal_format' => 'Rp ' . number_format($this->nominal, 0, ',', '.'),
            'tanggal' => $this->tanggal->format('Y-m-d'),
            'keterangan' => $this->keterangan,
            'bukti_nota' => $this->bukti_nota,
            'bukti_nota_url' => $this->bukti_nota_url,
            'created_by' => $this->createdBy?->name,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}