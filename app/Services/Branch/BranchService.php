<?php

namespace App\Services\Branch;

use App\Repositories\Contracts\Branch\BranchRepositoryInterface;

class BranchService
{
    protected $repository;

    public function __construct(BranchRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all()->load('company');
    }

    public function getAllByCompany(int $companyId)
    {
        return $this->repository->findWhere(['company_id' => $companyId]);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        // If this is the first branch for the company, make it the main branch
        if (!$this->repository->findMainBranch($data['company_id'])) {
            $data['is_main'] = true;
        }

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

    public function findByCompany(int $companyId)
    {
        return $this->repository->findByCompany($companyId);
    }

    public function findMainBranch(int $companyId)
    {
        return $this->repository->findMainBranch($companyId);
    }

    public function getAllWithRelations()
    {
        return $this->repository->all()->load('company');
    }
}
