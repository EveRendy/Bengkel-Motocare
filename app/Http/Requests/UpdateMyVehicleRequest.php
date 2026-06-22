<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateMyVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ambil parameter vehicle dari route
        $vehicle = $this->route('vehicle');
        return Gate::allows('update-own-vehicle', $vehicle);
    }

    public function rules(): array
    {
        // Perhatikan: license_plate tidak ada di dalam rules karena dilarang diubah pelanggan
        return [
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
}