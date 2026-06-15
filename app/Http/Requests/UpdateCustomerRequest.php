<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-customers');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $this->route('customer')->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }
}