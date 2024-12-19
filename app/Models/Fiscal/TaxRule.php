<?php

namespace App\Models\Fiscal;

use App\Models\BaseModel;
use App\Models\Company\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRule extends BaseModel
{
    use SoftDeletes;

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
        'is_active' => 'boolean'
    ];

    // Relaciones
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function regionalRules()
    {
        return $this->hasMany(RegionalTaxRule::class);
    }

    // Métodos
    public function calculateTax($amount, $location = null)
    {
        if (!$this->isApplicable($amount)) {
            return 0;
        }

        $rate = $this->getEffectiveRate($location);
        return $amount * ($rate / 100);
    }

    public function isApplicable($amount)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    protected function getEffectiveRate($location)
    {
        if (!$location) {
            return $this->rate;
        }

        $regionalRule = $this->regionalRules()
            ->where('country_id', $location['country_id'])
            ->where('state_id', $location['state_id'])
            ->where('city_id', $location['city_id'] ?? null)
            ->where('is_active', true)
            ->first();

        return $regionalRule ? $regionalRule->rate : $this->rate;
    }
}
