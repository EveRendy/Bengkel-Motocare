<?php

namespace App\Http\Requests;

use App\Models\ServiceOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create-booking');
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'order_date' => 'required|date|after_or_equal:today',
            'complaint' => 'required|string|min:5',
            'current_km' => 'required|integer|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $vehicleId = $this->input('vehicle_id');
            $customerId = auth()->user()->customer->id ?? null;

            // Pastikan kendaraan ini milik pelanggan yang sedang login
            $isOwner = \App\Models\Vehicle::where('id', $vehicleId)->where('customer_id', $customerId)->exists();
            if (!$isOwner) {
                $validator->errors()->add('vehicle_id', 'Kendaraan tidak valid atau bukan milik Anda.');
                return;
            }

            // Validasi: 1 kendaraan tidak boleh 2 order aktif
            $hasActiveOrder = ServiceOrder::where('vehicle_id', $vehicleId)
                ->whereNotIn('status', ['selesai', 'batal'])
                ->exists();

            if ($hasActiveOrder) {
                $validator->errors()->add('vehicle_id', 'Kendaraan ini masih memiliki order servis yang sedang berjalan. Anda tidak dapat membuat booking baru untuk kendaraan ini.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Pilih kendaraan yang akan diservis.',
            'order_date.required' => 'Pilih tanggal servis.',
            'order_date.after_or_equal' => 'Tanggal servis minimal hari ini.',
            'complaint.required' => 'Tuliskan keluhan atau masalah kendaraan.',
            'current_km.required' => 'Masukkan kilometer kendaraan saat ini.',
        ];
    }
}