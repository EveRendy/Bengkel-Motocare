<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceOrderSparepart extends Pivot
{
    protected $table = 'service_order_spareparts';
    public $incrementing = true;

    protected $fillable = [
        'service_order_id',
        'sparepart_id',
        'quantity',
        'price_at_time',
    ];

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }
}