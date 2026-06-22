<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMechanicJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        $job = $this->route('job');
        if (!$job) {
            return false;
        }
        
        $order = $job->serviceOrder;

        return $order
            && $this->user()
            && $this->user()->hasRole('mekanik')
            && $order->mechanic_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'inspection_result' => 'required|string|min:5',
            'service_actions' => 'required|string|min:5',
            'mechanic_notes' => 'nullable|string',
            'spareparts' => 'nullable|array',
            'spareparts.*.id' => 'nullable|exists:spareparts,id',
            'spareparts.*.quantity' => 'nullable|integer|min:1',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rows = collect($this->input('spareparts', []))
                ->filter(fn ($row) => !empty($row['id']));

            foreach ($rows as $index => $row) {
                if (empty($row['quantity'])) {
                    $validator->errors()->add("spareparts.$index.quantity", 'Jumlah sparepart wajib diisi.');
                    continue;
                }

                $sparepart = Sparepart::find($row['id']);
                if ($sparepart && $sparepart->stock_status !== 'tersedia') {
                    $validator->errors()->add("spareparts.$index.id", 'Sparepart harus berstatus tersedia.');
                }
            }
        });
    }
}
