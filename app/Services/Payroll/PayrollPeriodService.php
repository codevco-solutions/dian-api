<?php

namespace App\Services\Payroll;

use App\Repositories\Contracts\Payroll\PayrollPeriodRepositoryInterface;

class PayrollPeriodService extends BasePayrollService
{
    public function __construct(PayrollPeriodRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}