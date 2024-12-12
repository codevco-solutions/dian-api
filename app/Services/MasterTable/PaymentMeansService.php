<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\PaymentMeansRepositoryInterface;

class PaymentMeansService extends BaseMasterTableService
{
    public function __construct(PaymentMeansRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}