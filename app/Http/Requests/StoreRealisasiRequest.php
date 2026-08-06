<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRealisasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi per-bidang sudah dicek di controller (canInputBidang)
    }

    public function rules(): array
    {
        return [
            'rencana_kinerja_id' => ['required', 'integer', 'exists:rencana_kinerjas,id'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'realisasi_fisik' => ['required', 'numeric', 'min:0'],
            'realisasi_anggaran' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
            'bukti' => ['nullable', 'file', 'mimes:pdf', 'max:40960'],
        ];
    }

    public function messages(): array
    {
        return [
            'bukti.mimes' => 'Hanya menerima file PDF.',
            'bukti.max' => 'Maksimal ukuran file 40 MB.',
        ];
    }
}
