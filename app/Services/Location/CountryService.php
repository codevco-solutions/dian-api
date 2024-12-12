<?php

namespace App\Services\Location;

use App\Models\Country;
use App\Repositories\Contracts\Location\CountryRepositoryInterface;

class CountryService
{
    protected $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function all()
    {
        return $this->countryRepository->all();
    }

    public function find($id)
    {
        return $this->countryRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->countryRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->countryRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->countryRepository->delete($id);
    }
}
