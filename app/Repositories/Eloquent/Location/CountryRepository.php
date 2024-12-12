<?php

namespace App\Repositories\Eloquent\Location;

use App\Models\Country;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\Location\CountryRepositoryInterface;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }
}
