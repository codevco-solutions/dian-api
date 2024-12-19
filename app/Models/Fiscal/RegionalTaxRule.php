<?php

namespace App\Models\Fiscal;

use App\Models\BaseModel;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Location\City;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegionalTaxRule extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'tax_rule_id',
        'country_id',
        'state_id',
        'city_id',
        'rate',
        'is_active'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relaciones
    public function taxRule()
    {
        return $this->belongsTo(TaxRule::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Métodos
    public function getFullLocationName()
    {
        $parts = [
            $this->country->name,
            $this->state->name
        ];

        if ($this->city) {
            $parts[] = $this->city->name;
        }

        return implode(', ', $parts);
    }
}
