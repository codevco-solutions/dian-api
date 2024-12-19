<?php

namespace App\Repositories\Fiscal\Interfaces;

use App\Repositories\BaseRepositoryInterface;

interface RegionalTaxRuleRepositoryInterface extends BaseRepositoryInterface
{
    public function getByLocation($countryId, $stateId, $cityId = null);
    public function getByTaxRule($taxRuleId);
}
