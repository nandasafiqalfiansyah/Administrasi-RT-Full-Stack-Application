<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_rumah' => $this->nomor_rumah,
            'blok' => $this->blok,
            'status' => $this->status,
            'status_label' => $this->status === 'dihuni' ? 'Dihuni' : 'Tidak Dihuni',
            'current_resident_id' => $this->current_resident_id,
            'current_resident' => new ResidentResource($this->whenLoaded('currentResident')),
            'histories' => ResidentHouseHistoryResource::collection($this->whenLoaded('houseHistories')),
            'catatan' => $this->catatan,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}