<?php

namespace App\Services\Payroll;

use App\Repositories\Contracts\Payroll\EmployeeRepositoryInterface;

class EmployeeService extends BasePayrollService
{
    public function __construct(EmployeeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}