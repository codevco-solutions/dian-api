<?php

namespace App\Repositories\Contracts\User;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get user by email
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email);

    /**
     * Get users by company
     *
     * @param int $companyId
     * @return mixed
     */
    public function getAllByCompany(int $companyId);

    /**
     * Get users by branch
     *
     * @param int $branchId
     * @return mixed
     */
    public function getAllByBranch(int $branchId);

    /**
     * Get users by role
     *
     * @param int $roleId
     * @return mixed
     */
    public function getAllByRole(int $roleId);
}
