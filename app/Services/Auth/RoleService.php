<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Repositories\Contracts\Auth\RoleRepositoryInterface;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function all()
    {
        return $this->roleRepository->all();
    }

    public function find($id)
    {
        return $this->roleRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->roleRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->roleRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->roleRepository->delete($id);
    }
}
