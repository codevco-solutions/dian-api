<?php

namespace App\Services\Payroll;

use App\Repositories\Contracts\Payroll\PayrollDeductionRepositoryInterface;

class PayrollDeductionService extends BasePayrollService
{
    public function __construct(PayrollDeductionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}