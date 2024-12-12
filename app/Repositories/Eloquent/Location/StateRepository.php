<?php

namespace App\Repositories\Eloquent\Location;

use App\Models\State;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\Location\StateRepositoryInterface;

class StateRepository extends BaseRepository implements StateRepositoryInterface
{
    public function __construct(State $model)
    {
        parent::__construct($model);
    }
}
