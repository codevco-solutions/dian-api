<?php

namespace App\Services\Payroll;

use App\Repositories\Contracts\Payroll\PayrollAdjustmentRepositoryInterface;

class PayrollAdjustmentService extends BasePayrollService
{
    public function __construct(PayrollAdjustmentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}