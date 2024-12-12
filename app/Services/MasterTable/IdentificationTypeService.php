<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\IdentificationTypeRepositoryInterface;

class IdentificationTypeService extends BaseMasterTableService
{
    public function __construct(IdentificationTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}