<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi ditangani di controller via Gate
    }

    public function rules(): array
    {
        return [
            'complaint' => 'required|string',
            'current_km' => 'required|integer|min:0',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'mechanic_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'complaint.required' => 'Keluhan pelanggan wajib diisi/dijelaskan.',
            'current_km.required' => 'Kilometer saat ini wajib diisi.',
            'services.required' => 'Pilih minimal satu layanan servis.',
            'mechanic_id.exists' => 'Mekanik yang dipilih tidak valid.',
        ];
    }
}