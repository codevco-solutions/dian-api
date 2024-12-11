<?php

namespace App\Repositories\Contracts\Branch;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface BranchRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCompany(int $companyId);
    public function findMainBranch(int $companyId);
}
