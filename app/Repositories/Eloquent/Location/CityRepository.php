<?php

namespace App\Repositories\Eloquent\Location;

use App\Models\City;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\Location\CityRepositoryInterface;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }
}
