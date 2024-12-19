<?php

namespace App\Services\Fiscal;

use App\Models\Fiscal\DianResolution;
use App\Repositories\Contracts\Fiscal\DianResolutionRepositoryInterface;
use App\Exceptions\BusinessException;
use Illuminate\Pagination\LengthAwarePaginator;

class DianResolutionService
{
    protected $repository;

    public function __construct(DianResolutionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAll($filters);
    }

    public function findById(int $id): ?DianResolution
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): DianResolution
    {
        $this->validateResolutionData($data);
        return $this->repository->create($data);
    }

    public function update(DianResolution $resolution, array $data): DianResolution
    {
        if (isset($data['current_number'])) {
            $this->validateCurrentNumber($resolution, $data['current_number']);
        }
        return $this->repository->update($resolution, $data);
    }

    public function getNextNumber(DianResolution $resolution): array
    {
        if (!$resolution->isValid()) {
            throw new BusinessException('La resolución no es válida o ha expirado');
        }

        return [
            'next_number' => $this->repository->getNextNumber($resolution),
            'remaining_numbers' => $resolution->getRemainingNumbers(),
            'remaining_days' => $resolution->getRemainingDays()
        ];
    }

    protected function validateResolutionData(array $data): void
    {
        // Validar que no exista otra resolución activa para la misma compañía/sucursal/tipo
        $existingResolution = $this->repository->getValidResolution(
            $data['company_id'],
            $data['branch_id'],
            $data['type']
        );

        if ($existingResolution) {
            throw new BusinessException(
                'Ya existe una resolución válida para este tipo de documento'
            );
        }
    }

    protected function validateCurrentNumber(DianResolution $resolution, int $number): void
    {
        if ($number < $resolution->start_number || $number > $resolution->end_number) {
            throw new BusinessException(
                'El número actual debe estar entre el número inicial y final de la resolución'
            );
        }
    }
}
