<?php

namespace App\Services\Payroll;

use App\Repositories\Contracts\Payroll\PayrollDocumentRepositoryInterface;

class PayrollDocumentService extends BasePayrollService
{
    public function __construct(PayrollDocumentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}