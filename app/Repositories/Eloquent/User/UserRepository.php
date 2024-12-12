<?php

namespace App\Repositories\Eloquent\User;

use App\Models\User;
use App\Repositories\Contracts\User\UserRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get users by company
     *
     * @param int $companyId
     * @return mixed
     */
    public function getAllByCompany(int $companyId)
    {
        return $this->model->where('company_id', $companyId)
            ->with(['role', 'branch'])
            ->get();
    }

    /**
     * Get users by branch
     *
     * @param int $branchId
     * @return mixed
     */
    public function getAllByBranch(int $branchId)
    {
        return $this->model->where('branch_id', $branchId)
            ->with(['role', 'company'])
            ->get();
    }

    /**
     * Get users by role
     *
     * @param int $roleId
     * @return mixed
     */
    public function getAllByRole(int $roleId)
    {
        return $this->model->where('role_id', $roleId)
            ->with(['company', 'branch'])
            ->get();
    }
}
