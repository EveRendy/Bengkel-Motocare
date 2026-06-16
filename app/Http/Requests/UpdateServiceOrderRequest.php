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
        ];
    }
}