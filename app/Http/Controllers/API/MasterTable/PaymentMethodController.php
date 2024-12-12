<?php

namespace App\Http\Controllers\API\MasterTable;

use App\Http\Controllers\Controller;
use App\Services\MasterTable\MasterTableService;

class PaymentMethodController extends MasterTableController
{
    public function __construct(MasterTableService $service)
    {
        parent::__construct($service);
        $this->setTable('payment_methods');
    }
}
