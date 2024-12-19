<?php

namespace App\Services\Fiscal;

use App\Models\Fiscal\DocumentNumberingConfig;
use App\Repositories\Fiscal\Interfaces\DocumentNumberingConfigRepositoryInterface;
use App\Exceptions\BusinessException;
use Illuminate\Pagination\LengthAwarePaginator;

class DocumentNumberingConfigService
{
    protected $repository;

    public function __construct(DocumentNumberingConfigRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAll($filters);
    }

    public function findById(int $id): ?DocumentNumberingConfig
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): DocumentNumberingConfig
    {
        $this->validateConfigData($data);
        return $this->repository->create($data);
    }

    public function update(DocumentNumberingConfig $config, array $data): DocumentNumberingConfig
    {
        return $this->repository->update($config, $data);
    }

    public function generateNextNumber(DocumentNumberingConfig $config): array
    {
        if (!$config->is_active) {
            throw new BusinessException('La configuración de numeración no está activa');
        }

        return [
            'number' => $this->repository->generateNextNumber($config)
        ];
    }

    protected function validateConfigData(array $data): void
    {
        // Validar que no exista otra configuración activa para el mismo tipo de documento
        $existingConfig = $this->repository->getActiveConfig(
            $data['company_id'],
            $data['branch_id'],
            $data['document_type']
        );

        if ($existingConfig) {
            throw new BusinessException(
                'Ya existe una configuración activa para este tipo de documento'
            );
        }

        if (isset($data['padding']) && ($data['padding'] < 1 || $data['padding'] > 20)) {
            throw new BusinessException(
                'El relleno debe estar entre 1 y 20 dígitos'
            );
        }
    }
}
