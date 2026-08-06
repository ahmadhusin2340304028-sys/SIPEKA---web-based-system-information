<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUndanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'judul_kegiatan' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'waktu' => ['required'],
            'tempat' => ['required', 'string', 'max:255'],
            'pihak_mengundang' => ['required', 'string', 'max:255'],
            'status_kegiatan' => ['nullable', 'in:Belum Terlaksana,Terlaksana'],
            'notify_all' => ['nullable', 'boolean'],
            'bidang_terkait' => ['required', 'array', 'min:1'],
            'bidang_terkait.*' => ['integer', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'bidang_terkait.required' => 'Minimal pilih satu pihak terkait.',
            'bidang_terkait.min' => 'Minimal pilih satu pihak terkait.',
        ];
    }
}
