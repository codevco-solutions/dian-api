<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\OrganizationTypeRepositoryInterface;

class OrganizationTypeService extends BaseMasterTableService
{
    public function __construct(OrganizationTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}