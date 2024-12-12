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
        $query = Company::query();

        // Apply filters
        if (isset($filters['business_name'])) {
            $query->where('business_name', 'like', '%' . $filters['business_name'] . '%');
        }
        if (isset($filters['commercial_name'])) {
            $query->where('commercial_name', 'like', '%' . $filters['commercial_name'] . '%');
        }
        if (isset($filters['nit'])) {
            $query->where('nit', 'like', '%' . $filters['nit'] . '%');
        }
        if (isset($filters['address'])) {
            $query->where('address', 'like', '%' . $filters['address'] . '%');
        }
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Apply ordering
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        // Apply pagination
        return $query->paginate($perPage);
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
