<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\OperationTypeRepositoryInterface;

class OperationTypeService extends BaseMasterTableService
{
    public function __construct(OperationTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}