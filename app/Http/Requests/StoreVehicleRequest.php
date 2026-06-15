<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-vehicles');
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'license_plate' => 'required|string|max:20|unique:vehicles',
            'brand' => 'required|string|max:50',
            'type' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:2030',
            'color' => 'required|string|max:30',
            'chassis_number' => 'nullable|string|max:50',
            'engine_number' => 'nullable|string|max:50',
            'last_km' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}