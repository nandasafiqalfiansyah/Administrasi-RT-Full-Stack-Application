<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidentHouseHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'house_id' => $this->house_id,
            'resident_id' => $this->resident_id,
            'resident' => new ResidentResource($this->whenLoaded('resident')),
            'tanggal_masuk' => $this->tanggal_masuk->format('Y-m-d'),
            'tanggal_keluar' => $this->tanggal_keluar?->format('Y-m-d'),
            'status' => $this->status,
            'status_label' => $this->status === 'tetap' ? 'Tetap' : 'Kontrak',
            'catatan' => $this->catatan,
        ];
    }
}