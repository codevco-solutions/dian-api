<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    protected $fillable = [
        'country_id',
        'state_id',
        'city_id',
        'address_line1',
        'address_line2',
        'postal_code',
        'phone',
        'mobile',
        'email',
        'contact_person',
        'is_main',
        'is_billing',
        'is_shipping',
        'is_active'
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_billing' => 'boolean',
        'is_shipping' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\MasterTable\Country::class);
    }

    public function state()
    {
        return $this->belongsTo(\App\Models\MasterTable\State::class);
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\MasterTable\City::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    public function scopeBilling($query)
    {
        return $query->where('is_billing', true);
    }

    public function scopeShipping($query)
    {
        return $query->where('is_shipping', true);
    }
}
