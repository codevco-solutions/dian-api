<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\TaxRegimeRepositoryInterface;

class TaxRegimeService extends BaseMasterTableService
{
    public function __construct(TaxRegimeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}