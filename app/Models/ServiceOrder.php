<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceOrder extends Model
{
    protected $fillable = [
        'order_number',
        'order_date',
        'customer_id',
        'vehicle_id',
        'service_advisor_id',
        'mechanic_id',
        'complaint',
        'current_km',
        'estimated_cost',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function serviceAdvisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'service_advisor_id');
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_order_services')
            ->withPivot('price_at_time')
            ->withTimestamps();
    }

    public function spareparts(): BelongsToMany
    {
        return $this->belongsToMany(Sparepart::class, 'service_order_spareparts')
            ->withPivot('quantity', 'price_at_time')
            ->withTimestamps();
    }

    public function mechanicJob(): HasOne
    {
        return $this->hasOne(MechanicJob::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}