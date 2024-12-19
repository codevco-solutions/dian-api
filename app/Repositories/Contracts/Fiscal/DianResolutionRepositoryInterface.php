<?php

namespace App\Repositories\Contracts\Fiscal;

use App\Models\Fiscal\DianResolution;
use Illuminate\Pagination\LengthAwarePaginator;

interface DianResolutionRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?DianResolution;
    public function create(array $data): DianResolution;
    public function update(DianResolution $resolution, array $data): DianResolution;
    public function delete(int $id): bool;
    public function getNextNumber(DianResolution $resolution): int;
    public function getValidResolution(int $companyId, int $branchId, string $type): ?DianResolution;
}
