<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Location\City;

class RegionalTaxRule extends Model
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
        'is_active' => 'boolean',
    ];

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
}
