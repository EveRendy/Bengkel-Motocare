<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-customers');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'registration_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan pelanggan lain.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'registration_date.required' => 'Tanggal pendaftaran wajib diisi.',
        ];
    }
}