<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSparepartStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-spareparts');
    }

    public function rules(): array
    {
        return [
            'stock_status' => ['required', 'in:tersedia,habis,nonaktif'],
        ];
    }
}
