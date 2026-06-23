<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSparepartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-spareparts');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_status' => ['required', 'in:tersedia,habis,nonaktif'],
        ];
    }
}
