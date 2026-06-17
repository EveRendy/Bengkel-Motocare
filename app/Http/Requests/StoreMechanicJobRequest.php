<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMechanicJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi sudah dilakukan di Controller via Gate
    }

    public function rules(): array
    {
        return [
            'inspection_result' => 'required|string|min:5',
            'service_actions' => 'required|string|min:5',
            'mechanic_notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'inspection_result.required' => 'Hasil pemeriksaan wajib diisi.',
            'inspection_result.min' => 'Hasil pemeriksaan terlalu singkat.',
            'service_actions.required' => 'Tindakan servis wajib diisi.',
        ];
    }
}