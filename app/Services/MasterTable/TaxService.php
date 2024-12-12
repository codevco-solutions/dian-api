<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\TaxRepositoryInterface;

class TaxService extends BaseMasterTableService
{
    public function __construct(TaxRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}