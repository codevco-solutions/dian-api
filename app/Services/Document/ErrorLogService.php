<?php

namespace App\Services\Document;

use App\Repositories\Contracts\Document\ErrorLogRepositoryInterface;

class ErrorLogService
{
    protected $repository;

    public function __construct(ErrorLogRepositoryInterface $repository)
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
}