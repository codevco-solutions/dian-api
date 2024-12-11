<?php

namespace App\Repositories\Contracts\Company;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface CompanyRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySubdomain(string $subdomain);
    public function findByTaxId(string $taxId);
}
