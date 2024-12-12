<?php

namespace App\Services\Payroll;

use App\Repositories\Contracts\Payroll\EmployeeContractRepositoryInterface;

class EmployeeContractService extends BasePayrollService
{
    public function __construct(EmployeeContractRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}