<?php

namespace App\Repositories\Fiscal;

use App\Models\Fiscal\DocumentNumberingConfig;
use App\Repositories\Fiscal\Interfaces\DocumentNumberingConfigRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class DocumentNumberingConfigRepository implements DocumentNumberingConfigRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return DocumentNumberingConfig::query()
            ->when(isset($filters['company_id']), fn($q) => $q->where('company_id', $filters['company_id']))
            ->when(isset($filters['branch_id']), fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(isset($filters['document_type']), fn($q) => $q->where('document_type', $filters['document_type']))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?DocumentNumberingConfig
    {
        return DocumentNumberingConfig::find($id);
    }

    public function create(array $data): DocumentNumberingConfig
    {
        $data['last_number'] = 0;
        return DocumentNumberingConfig::create($data);
    }

    public function update(DocumentNumberingConfig $config, array $data): DocumentNumberingConfig
    {
        $config->update($data);
        return $config->fresh();
    }

    public function delete(int $id): bool
    {
        return DocumentNumberingConfig::destroy($id) > 0;
    }

    public function generateNextNumber(DocumentNumberingConfig $config): string
    {
        return $config->generateNextNumber();
    }

    public function getActiveConfig(int $companyId, int $branchId, string $documentType): ?DocumentNumberingConfig
    {
        return DocumentNumberingConfig::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('document_type', $documentType)
            ->where('is_active', true)
            ->first();
    }
}
