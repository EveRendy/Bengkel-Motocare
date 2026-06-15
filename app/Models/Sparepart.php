<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sparepart extends Model
{
    protected $fillable = [
        'part_code',
        'name',
        'price',
        'stock_status',
    ];

    public function serviceOrders(): BelongsToMany
    {
        return $this->belongsToMany(ServiceOrder::class, 'service_order_spareparts')
            ->withPivot('quantity', 'price_at_time')
            ->withTimestamps();
    }
}