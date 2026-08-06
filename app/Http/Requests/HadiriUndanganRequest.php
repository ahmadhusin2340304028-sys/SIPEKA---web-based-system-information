<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HadiriUndanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gambar' => ['nullable', 'file', 'mimes:pdf', 'max:40960'],
            'keterangan' => ['nullable', 'string'],
        ];
    }
}
