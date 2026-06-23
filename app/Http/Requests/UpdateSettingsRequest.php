<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-settings');
    }

    public function rules(): array
    {
        return [
            'workshop_name' => ['required', 'string', 'max:100'],
            'workshop_tagline' => ['nullable', 'string', 'max:150'],
            'workshop_address' => ['nullable', 'string', 'max:255'],
            'workshop_phone' => ['nullable', 'string', 'max:30'],
            'workshop_email' => ['nullable', 'email', 'max:100'],
            'workshop_operational_hours' => ['nullable', 'string', 'max:120'],
            'workshop_logo_text' => ['nullable', 'string', 'max:10'],
        ];
    }
}
