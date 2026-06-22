<?php

namespace App\Http\Requests;

use App\Models\ServiceOrder;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-orders');
    }

    public function rules(): array
    {
        $rules = [
            'customer_type' => 'required|in:registered,walkin',
            'order_date' => 'required|date',
            'complaint' => 'required|string',
            'current_km' => 'required|integer|min:0',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'mechanic_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];

        if ($this->input('customer_type') === 'registered') {
            $rules['customer_id'] = 'required|exists:customers,id';
            $rules['vehicle_id'] = 'required|exists:vehicles,id';
        } else {
            $rules['walkin_customer_name'] = 'required|string|max:255';
            $rules['walkin_customer_phone'] = 'required|string|max:20';
            $rules['walkin_vehicle_license_plate'] = 'required|string|max:20';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('customer_type') === 'registered') {
                $customerId = $this->input('customer_id');
                $vehicleId = $this->input('vehicle_id');

                // Cek apakah kendaraan benar milik customer
                $isOwner = Vehicle::where('id', $vehicleId)->where('customer_id', $customerId)->exists();
                if (!$isOwner) {
                    $validator->errors()->add('vehicle_id', 'Kendaraan tidak sesuai dengan pelanggan yang dipilih.');
                    return;
                }

                // Cek order aktif untuk kendaraan (Tidak boleh dobel servis)
                $hasActiveOrder = ServiceOrder::where('vehicle_id', $vehicleId)
                    ->whereNotIn('status', ['selesai', 'batal'])
                    ->exists();

                if ($hasActiveOrder) {
                    $validator->errors()->add('vehicle_id', 'Kendaraan ini sedang memiliki antrean/proses servis yang aktif.');
                }
            }
        });
    }
}