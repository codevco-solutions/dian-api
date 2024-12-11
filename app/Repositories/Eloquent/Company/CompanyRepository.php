<?php

namespace App\Repositories\Eloquent\Company;

use App\Models\Company;
use App\Repositories\Contracts\Company\CompanyRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }

    public function findBySubdomain(string $subdomain)
    {
        return $this->model->where('subdomain', $subdomain)->first();
    }

    public function findByTaxId(string $taxId)
    {
        return $this->model->where('tax_id', $taxId)->first();
    }
}
