<?php

namespace App\Repositories\Fiscal\Interfaces;

use App\Models\Fiscal\TaxRule;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaxRuleRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?TaxRule;
    public function create(array $data): TaxRule;
    public function update(TaxRule $rule, array $data): TaxRule;
    public function delete(int $id): bool;
    public function calculateTax(TaxRule $rule, float $amount, ?array $location = null): float;
}
