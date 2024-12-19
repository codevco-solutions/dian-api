<?php

namespace App\Services\Fiscal;

use App\Services\BaseService;
use App\Repositories\Fiscal\Interfaces\RegionalTaxRuleRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ValidationException;

class RegionalTaxRuleService extends BaseService
{
    protected $repository;

    public function __construct(RegionalTaxRuleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function validateData(array $data, $id = null): array
    {
        $rules = [
            'tax_rule_id' => 'required|exists:tax_rules,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function getByLocation($countryId, $stateId, $cityId = null)
    {
        return $this->repository->getByLocation($countryId, $stateId, $cityId);
    }

    public function getByTaxRule($taxRuleId)
    {
        return $this->repository->getByTaxRule($taxRuleId);
    }
}
