<?php

namespace App\Services\Payroll;

use App\Repositories\Contracts\Payroll\PayrollEarningRepositoryInterface;

class PayrollEarningService extends BasePayrollService
{
    public function __construct(PayrollEarningRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}