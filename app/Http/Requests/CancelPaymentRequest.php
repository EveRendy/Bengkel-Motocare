<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CancelPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('cancel-payment');
    }

    public function rules(): array
    {
        return [
            'cancel_reason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }
}
