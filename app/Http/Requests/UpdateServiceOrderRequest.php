<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-orders');
    }

    public function rules(): array
    {
        return [
            'order_date' => 'required|date',
            'complaint' => 'required|string',
            'current_km' => 'required|integer|min:0',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'mechanic_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            // Walk-in customer fields
            'walkin_customer_name' => 'nullable|string|max:255',
            'walkin_customer_phone' => 'nullable|string|max:20',
            'walkin_customer_email' => 'nullable|email',
            'walkin_customer_address' => 'nullable|string',
            // Walk-in vehicle fields
            'walkin_vehicle_license_plate' => 'nullable|string|max:20',
            'walkin_vehicle_brand' => 'nullable|string|max:255',
            'walkin_vehicle_type' => 'nullable|string|max:255',
            'walkin_vehicle_year' => 'nullable|integer',
            'walkin_vehicle_color' => 'nullable|string|max:255',
        ];
    }
}