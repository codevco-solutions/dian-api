<?php

namespace App\Http\Controllers\API\MasterTable;

use App\Http\Controllers\Controller;
use App\Services\MasterTable\MasterTableService;

class ReferencePriceController extends MasterTableController
{
    public function __construct(MasterTableService $service)
    {
        parent::__construct($service);
        $this->setTable('reference_prices');
    }
}
