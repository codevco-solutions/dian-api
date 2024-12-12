<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\Role;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\Auth\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
