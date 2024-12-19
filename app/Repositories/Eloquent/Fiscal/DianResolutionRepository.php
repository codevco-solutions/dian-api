<?php

namespace App\Repositories\Eloquent\Fiscal;

use App\Models\Fiscal\DianResolution;
use App\Repositories\Contracts\Fiscal\DianResolutionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class DianResolutionRepository implements DianResolutionRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return DianResolution::query()
            ->when(isset($filters['company_id']), fn($q) => $q->where('company_id', $filters['company_id']))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(isset($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?DianResolution
    {
        return DianResolution::find($id);
    }

    public function create(array $data): DianResolution
    {
        $data['current_number'] = $data['start_number'] - 1;
        $data['is_active'] = $data['is_active'] ?? true;

        return DianResolution::create($data);
    }

    public function update(DianResolution $resolution, array $data): DianResolution
    {
        $resolution->update($data);
        return $resolution->fresh();
    }

    public function delete(int $id): bool
    {
        return DianResolution::destroy($id) > 0;
    }

    public function getNextNumber(DianResolution $resolution): int
    {
        return $resolution->getNextNumber();
    }

    public function getValidResolution(int $companyId, int $branchId, string $type): ?DianResolution
    {
        return DianResolution::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('type', $type)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('current_number', '<', \DB::raw('end_number'))
            ->first();
    }
}
