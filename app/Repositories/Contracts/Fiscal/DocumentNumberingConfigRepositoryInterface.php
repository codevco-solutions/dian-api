<?php

namespace App\Repositories\Fiscal\Interfaces;

use App\Models\Fiscal\DocumentNumberingConfig;
use Illuminate\Pagination\LengthAwarePaginator;

interface DocumentNumberingConfigRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?DocumentNumberingConfig;
    public function create(array $data): DocumentNumberingConfig;
    public function update(DocumentNumberingConfig $config, array $data): DocumentNumberingConfig;
    public function delete(int $id): bool;
    public function generateNextNumber(DocumentNumberingConfig $config): string;
    public function getActiveConfig(int $companyId, int $branchId, string $documentType): ?DocumentNumberingConfig;
}
