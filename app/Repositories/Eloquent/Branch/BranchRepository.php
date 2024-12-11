<?php

namespace App\Repositories\Eloquent\Branch;

use App\Models\Branch;
use App\Repositories\Contracts\Branch\BranchRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class BranchRepository extends BaseRepository implements BranchRepositoryInterface
{
    public function __construct(Branch $model)
    {
        parent::__construct($model);
    }

    public function findByCompany(int $companyId)
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function findMainBranch(int $companyId)
    {
        return $this->model->where('company_id', $companyId)
            ->where('is_main', true)
            ->first();
    }
}
