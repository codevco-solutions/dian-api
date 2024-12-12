<?php

namespace App\Services\Common;

use App\Repositories\Contracts\Common\AddressRepositoryInterface;

class AddressService
{
    protected $repository;

    public function __construct(AddressRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function findByEntity(string $entityType, int $entityId)
    {
        return $this->repository->findByEntity($entityType, $entityId);
    }
}
