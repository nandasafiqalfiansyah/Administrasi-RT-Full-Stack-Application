<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'house_id' => 'required|exists:houses,id',
            'resident_id' => 'nullable|exists:residents,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'bulan' => 'nullable|integer|between:1,12',
            'bulan_mulai' => 'nullable|integer|between:1,12',
            'bulan_selesai' => 'nullable|integer|between:1,12|gte:bulan_mulai',
            'tahun' => 'nullable|integer|min:2020|max:2099',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,lainnya',
            'bukti_bayar' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'keterangan' => 'nullable|string',
        ];
    }
}