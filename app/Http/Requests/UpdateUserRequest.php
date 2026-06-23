<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-users');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', Rule::exists('roles', 'id')->where(fn ($query) => $query->where('name', '!=', 'pelanggan'))],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }
}
