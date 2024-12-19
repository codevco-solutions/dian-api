<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Company;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Location\City;

class TaxRule extends Model
{
    use SoftDeletes;

    protected $table = 'tax_rules';

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'rate',
        'min_amount',
        'max_amount',
        'conditions',
        'is_active'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'conditions' => 'json',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function regionalRules()
    {
        return $this->hasMany(RegionalTaxRule::class);
    }
}
