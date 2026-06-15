<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceOrderService extends Pivot
{
    protected $table = 'service_order_services';
    public $incrementing = true;

    protected $fillable = [
        'service_order_id',
        'service_id',
        'price_at_time',
    ];

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}