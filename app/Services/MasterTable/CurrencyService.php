<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\CurrencyRepositoryInterface;

class CurrencyService extends BaseMasterTableService
{
    public function __construct(CurrencyRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}