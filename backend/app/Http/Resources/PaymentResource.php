<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_pembayaran' => $this->kode_pembayaran,
            'house' => new HouseSimpleResource($this->whenLoaded('house')),
            'resident' => new ResidentResource($this->whenLoaded('resident')),
            'payment_type' => [
                'id' => $this->paymentType?->id,
                'nama' => $this->paymentType?->nama,
                'nominal' => $this->paymentType?->nominal,
            ],
            'nominal' => (float) $this->nominal,
            'nominal_format' => 'Rp ' . number_format($this->nominal, 0, ',', '.'),
            'tanggal_bayar' => $this->tanggal_bayar->format('Y-m-d'),
            'metode_pembayaran' => $this->metode_pembayaran,
            'metode_label' => ucfirst($this->metode_pembayaran),
            'bukti_bayar' => $this->bukti_bayar,
            'bukti_bayar_url' => $this->bukti_bayar_url,
            'keterangan' => $this->keterangan,
            'created_by' => $this->createdBy?->name,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}