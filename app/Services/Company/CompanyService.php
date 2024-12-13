<?php

namespace App\Services\Company;

use App\Models\Company\Company;
use App\Repositories\Contracts\Company\CompanyRepositoryInterface;
use Illuminate\Support\Str;

class CompanyService
{
    protected $repository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll($perPage = 15, array $filters = [], array $orderBy = ['created_at' => 'desc'])
    {
        return $this->repository->getAll($perPage, $filters, $orderBy);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        // Generate subdomain from name if not provided
        if (!isset($data['subdomain']) && isset($data['name'])) {
            $data['subdomain'] = Str::slug($data['name']);
        }
        
        $company = $this->repository->create($data);
        
        // Create main branch
        $this->createMainBranch($company);
        
        return $company;
    }

    public function update(int $id, array $data)
    {
        $company = $this->repository->find($id);
        
        // Generate subdomain from name if name is provided and subdomain is not
        if (!isset($data['subdomain']) && isset($data['name'])) {
            $data['subdomain'] = Str::slug($data['name']);
        }
        
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        $company = $this->repository->find($id);
        
        // Primero eliminamos todas las sucursales
        $company->branches()->delete();
        
        // Luego eliminamos la compañía
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

    public function createMainBranch($company)
    {
        $branchCode = 'MAIN-' . uniqid();
        
        return $company->branches()->create([
            'name' => 'Principal',
            'code' => $branchCode,
            'address' => $company->address,
            'phone' => $company->phone,
            'email' => $company->email,
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'is_main' => true,
        ]);
    }
}
