<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\ChargeTypeRepositoryInterface;

class ChargeTypeService extends BaseMasterTableService
{
    public function __construct(ChargeTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}