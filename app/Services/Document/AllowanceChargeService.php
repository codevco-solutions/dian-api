<?php

namespace App\Services\Document;

use App\Repositories\Contracts\AllowanceChargeRepositoryInterface;

class AllowanceChargeService
{
    protected $repository;

    public function __construct(AllowanceChargeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        // Validar y calcular montos
        if (isset($data['base_amount']) && isset($data['multiplier_factor_numeric'])) {
            $data['amount'] = $data['base_amount'] * $data['multiplier_factor_numeric'];
        }

        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        // Validar y recalcular montos si es necesario
        if (isset($data['base_amount']) && isset($data['multiplier_factor_numeric'])) {
            $data['amount'] = $data['base_amount'] * $data['multiplier_factor_numeric'];
        }

        return $this->repository->update($id, $data);
    }

    public function getByDocument($documentType, $documentId)
    {
        return $this->repository->findByDocument($documentType, $documentId);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
