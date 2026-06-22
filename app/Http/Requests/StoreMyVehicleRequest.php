<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreMyVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-own-vehicles');
    }

    public function rules(): array
    {
        return [
            'license_plate' => 'required|string|unique:vehicles,license_plate',
            'brand' => 'required|string',
            'type' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string',
            'chassis_number' => 'nullable|string',
            'engine_number' => 'nullable|string',
            'last_km' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'license_plate.required' => 'Plat nomor wajib diisi.',
            'license_plate.unique' => 'Plat nomor ini sudah terdaftar di sistem kami.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}