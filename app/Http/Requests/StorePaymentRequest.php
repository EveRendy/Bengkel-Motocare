<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('process-payment');
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:cash,transfer,qris',
            'discount' => 'nullable|numeric|min:0',
        ];
    }
}
