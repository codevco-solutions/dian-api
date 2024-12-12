<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\TaxResponsibilityRepositoryInterface;

class TaxResponsibilityService extends BaseMasterTableService
{
    public function __construct(TaxResponsibilityRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}