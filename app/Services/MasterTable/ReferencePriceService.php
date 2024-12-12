<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\ReferencePriceRepositoryInterface;

class ReferencePriceService extends BaseMasterTableService
{
    public function __construct(ReferencePriceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}