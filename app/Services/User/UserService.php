<?php

namespace App\Services\User;

use App\Repositories\Contracts\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->userRepository->all();
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->userRepository->find($id);
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get users by company
     *
     * @param int $companyId
     * @return mixed
     */
    public function getAllByCompany(int $companyId)
    {
        return $this->userRepository->getAllByCompany($companyId);
    }

    /**
     * Get users by branch
     *
     * @param int $branchId
     * @return mixed
     */
    public function getAllByBranch(int $branchId)
    {
        return $this->userRepository->getAllByBranch($branchId);
    }

    /**
     * Get users by role
     *
     * @param int $roleId
     * @return mixed
     */
    public function getAllByRole(int $roleId)
    {
        return $this->userRepository->getAllByRole($roleId);
    }

    /**
     * Create new user
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        // Hash password if present
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->create($data);
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        // Hash password if present
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($id, $data);
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}
