<?php

namespace App\Services\Fiscal;

use App\Models\Fiscal\TaxRule;
use App\Repositories\Fiscal\Interfaces\TaxRuleRepositoryInterface;
use App\Exceptions\BusinessException;
use Illuminate\Pagination\LengthAwarePaginator;

class TaxRuleService
{
    protected $repository;

    public function __construct(TaxRuleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAll($filters);
    }

    public function findById(int $id): ?TaxRule
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): TaxRule
    {
        $this->validateTaxRuleData($data);
        return $this->repository->create($data);
    }

    public function update(TaxRule $rule, array $data): TaxRule
    {
        if (isset($data['rate'])) {
            $this->validateRate($data['rate']);
        }
        return $this->repository->update($rule, $data);
    }

    public function calculateTax(TaxRule $rule, float $amount, ?array $location = null): array
    {
        if (!$rule->is_active) {
            throw new BusinessException('La regla de impuestos no está activa');
        }

        $taxAmount = $this->repository->calculateTax($rule, $amount, $location);

        return [
            'amount' => $amount,
            'tax_rate' => $rule->getEffectiveRate($location),
            'tax_amount' => $taxAmount,
            'total_amount' => $amount + $taxAmount
        ];
    }

    protected function validateTaxRuleData(array $data): void
    {
        $this->validateRate($data['rate']);

        if (isset($data['min_amount']) && isset($data['max_amount'])) {
            if ($data['max_amount'] <= $data['min_amount']) {
                throw new BusinessException(
                    'El monto máximo debe ser mayor que el monto mínimo'
                );
            }
        }
    }

    protected function validateRate(float $rate): void
    {
        if ($rate < 0 || $rate > 100) {
            throw new BusinessException(
                'La tasa debe estar entre 0 y 100'
            );
        }
    }
}
