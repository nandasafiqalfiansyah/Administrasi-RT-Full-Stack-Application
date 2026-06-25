<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('house') ?? 'null';
        return [
            'nomor_rumah' => "required|string|max:10|unique:houses,nomor_rumah,{$id}",
            'blok' => 'nullable|string|max:10',
            'status' => 'required|in:dihuni,tidak_dihuni',
            'current_resident_id' => 'nullable|exists:residents,id',
            'status_penghuni' => 'nullable|in:tetap,kontrak',
            'catatan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_rumah.required' => 'Nomor rumah wajib diisi',
            'nomor_rumah.unique' => 'Nomor rumah sudah terdaftar',
            'status.required' => 'Status rumah wajib dipilih',
            'current_resident_id.exists' => 'Penghuni tidak ditemukan',
        ];
    }
}