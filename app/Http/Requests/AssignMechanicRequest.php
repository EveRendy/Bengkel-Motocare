<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class AssignMechanicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('assign-mechanic');
    }

    public function rules(): array
    {
        return [
            'mechanic_id' => ['required', 'exists:users,id'],
        ];
    }
}
