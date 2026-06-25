<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('resident') ?? 'null';
        return [
            'nik' => "required|string|size:16|unique:residents,nik,{$id}",
            'nama_lengkap' => 'required|string|max:255',
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:tetap,kontrak',
            'nomor_hp' => 'required|string|max:20',
            'status_menikah' => 'required|in:belum_kawin,kawin,cerai_hidup,cerai_mati',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:255',
            'tanggal_masuk' => 'required|date',
            'catatan' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 karakter',
            'nik.unique' => 'NIK sudah terdaftar',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus Tetap atau Kontrak',
            'nomor_hp.required' => 'Nomor HP wajib diisi',
            'status_menikah.required' => 'Status menikah wajib dipilih',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi',
        ];
    }
}