<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMechanicJobSparepartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sparepart_id' => [
                'required',
                'exists:spareparts,id',
                // Custom rule: pastikan stok tersedia
                function ($attribute, $value, $fail) {
                    $sparepart = \App\Models\Sparepart::find($value);
                    if ($sparepart && $sparepart->stock_status !== 'tersedia') {
                        $fail('Sparepart yang Anda pilih stoknya sedang habis atau nonaktif.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:1',
        ];
    }
}