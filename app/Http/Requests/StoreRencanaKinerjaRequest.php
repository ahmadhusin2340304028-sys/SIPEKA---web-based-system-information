<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRencanaKinerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'sasaran' => ['required', 'string'],
            'indikator' => ['required', 'string'],
            'program' => ['required', 'string'],
            'kegiatan' => ['required', 'string'],
            'subkegiatan' => ['required', 'string'],
            'satuan' => ['required', 'string', 'max:50'],
            'target' => ['required', 'numeric', 'min:0'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'pagu_anggaran' => ['required', 'numeric', 'min:0'],
            'bidang_id' => ['required', 'integer', 'exists:bidangs,id'],
        ];
    }
}
