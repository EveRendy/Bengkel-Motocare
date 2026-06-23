<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-services');
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
