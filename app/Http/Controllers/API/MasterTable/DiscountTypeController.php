<?php

namespace App\Http\Controllers\API\MasterTable;

use App\Http\Controllers\Controller;
use App\Services\MasterTable\MasterTableService;

class DiscountTypeController extends MasterTableController
{
    public function __construct(MasterTableService $service)
    {
        parent::__construct($service);
        $this->setTable('discount_types');
    }
}
