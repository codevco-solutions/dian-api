<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\EventTypeRepositoryInterface;

class EventTypeService extends BaseMasterTableService
{
    public function __construct(EventTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}