<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\PaymentMethodRepositoryInterface;

class PaymentMethodService extends BaseMasterTableService
{
    public function __construct(PaymentMethodRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}