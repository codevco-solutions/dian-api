<?php

namespace App\Services\Location;

use App\Models\City;
use App\Repositories\Contracts\Location\CityRepositoryInterface;

class CityService
{
    protected $cityRepository;

    public function __construct(CityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function all()
    {
        return $this->cityRepository->all();
    }

    public function find($id)
    {
        return $this->cityRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->cityRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->cityRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->cityRepository->delete($id);
    }
}
