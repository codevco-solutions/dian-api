<?php

namespace App\Repositories\Fiscal;

use App\Models\Fiscal\TaxRule;
use App\Repositories\Fiscal\Interfaces\TaxRuleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TaxRuleRepository implements TaxRuleRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return TaxRule::query()
            ->when(isset($filters['company_id']), fn($q) => $q->where('company_id', $filters['company_id']))
            ->when(isset($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
            ->with(['regionalRules'])
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?TaxRule
    {
        return TaxRule::with('regionalRules')->find($id);
    }

    public function create(array $data): TaxRule
    {
        return TaxRule::create($data);
    }

    public function update(TaxRule $rule, array $data): TaxRule
    {
        $rule->update($data);
        return $rule->fresh();
    }

    public function delete(int $id): bool
    {
        return TaxRule::destroy($id) > 0;
    }

    public function calculateTax(TaxRule $rule, float $amount, ?array $location = null): float
    {
        return $rule->calculateTax($amount, $location);
    }
}
