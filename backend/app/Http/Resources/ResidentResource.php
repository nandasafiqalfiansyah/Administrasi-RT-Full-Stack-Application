<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nik' => $this->nik,
            'nama_lengkap' => $this->nama_lengkap,
            'foto_ktp' => $this->foto_ktp,
            'foto_ktp_url' => $this->foto_ktp_url,
            'status' => $this->status,
            'status_label' => $this->status === 'tetap' ? 'Tetap' : 'Kontrak',
            'nomor_hp' => $this->nomor_hp,
            'status_menikah' => $this->status_menikah,
            'status_menikah_label' => $this->statusMenikahLabel(),
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'agama' => $this->agama,
            'pekerjaan' => $this->pekerjaan,
            'tanggal_masuk' => $this->tanggal_masuk->format('Y-m-d'),
            'catatan' => $this->catatan,
            'is_active' => $this->is_active,
            'current_house' => new HouseSimpleResource($this->whenLoaded('currentHouse')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function statusMenikahLabel(): string
    {
        return match ($this->status_menikah) {
            'belum_kawin' => 'Belum Kawin',
            'kawin' => 'Kawin',
            'cerai_hidup' => 'Cerai Hidup',
            'cerai_mati' => 'Cerai Mati',
            default => $this->status_menikah,
        };
    }
}