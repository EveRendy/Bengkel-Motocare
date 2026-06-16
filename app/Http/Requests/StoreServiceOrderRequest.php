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
        return [
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'order_date' => 'required|date',
            'complaint' => 'required|string',
            'current_km' => 'required|integer|min:0',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'mechanic_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
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
        });
    }
}