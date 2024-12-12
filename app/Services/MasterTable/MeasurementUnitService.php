<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\MeasurementUnitRepositoryInterface;

class MeasurementUnitService extends BaseMasterTableService
{
    public function __construct(MeasurementUnitRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}