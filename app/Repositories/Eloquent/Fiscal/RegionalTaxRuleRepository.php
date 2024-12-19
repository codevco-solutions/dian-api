<?php

namespace App\Repositories\Fiscal;

use App\Models\Fiscal\RegionalTaxRule;
use App\Repositories\BaseRepository;
use App\Repositories\Fiscal\Interfaces\RegionalTaxRuleRepositoryInterface;

class RegionalTaxRuleRepository extends BaseRepository implements RegionalTaxRuleRepositoryInterface
{
    public function __construct(RegionalTaxRule $model)
    {
        parent::__construct($model);
    }

    public function getByLocation($countryId, $stateId, $cityId = null)
    {
        return $this->model
            ->where('country_id', $countryId)
            ->where('state_id', $stateId)
            ->where(function ($query) use ($cityId) {
                $query->where('city_id', $cityId)
                    ->orWhereNull('city_id');
            })
            ->where('is_active', true)
            ->get();
    }

    public function getByTaxRule($taxRuleId)
    {
        return $this->model
            ->where('tax_rule_id', $taxRuleId)
            ->where('is_active', true)
            ->with(['country', 'state', 'city'])
            ->get();
    }
}
