<?php

namespace App\Services\Company;

use App\Models\Company;
use App\Repositories\Contracts\Company\CompanyRepositoryInterface;
use Illuminate\Support\Str;

class CompanyService
{
    protected $repository;

    public function __construct(CompanyRepositoryInterface $repository)
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
        // Generate subdomain from business name if not provided
        if (!isset($data['subdomain']) && isset($data['business_name'])) {
            $data['subdomain'] = Str::slug($data['business_name']);
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

    public function findBySubdomain(string $subdomain)
    {
        return $this->repository->findBySubdomain($subdomain);
    }

    public function findByTaxId(string $taxId)
    {
        return $this->repository->findByTaxId($taxId);
    }

    public function createMainBranch(Company $company)
    {
        return $company->branches()->create([
            'name' => 'Principal',
            'code' => 'MAIN-' . $company->id,
            'address' => $company->address,
            'phone' => $company->phone,
            'email' => $company->email,
            'is_main' => true,
        ]);
    }
}
