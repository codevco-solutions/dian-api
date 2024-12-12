<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\LocationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LocationService
{
    protected $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    // Countries
    public function getAllCountries(array $filters = [])
    {
        return $this->locationRepository->getAllCountries($filters);
    }

    public function getCountryById(int $id)
    {
        return $this->locationRepository->getCountryById($id);
    }

    public function createCountry(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->locationRepository->createCountry($data);
        });
    }

    public function updateCountry(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->locationRepository->updateCountry($id, $data);
        });
    }

    public function deleteCountry(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->locationRepository->deleteCountry($id);
        });
    }

    // States
    public function getAllStates(array $filters = [])
    {
        return $this->locationRepository->getAllStates($filters);
    }

    public function getStatesByCountry(int $countryId, array $filters = [])
    {
        return $this->locationRepository->getStatesByCountry($countryId, $filters);
    }

    public function getStateById(int $id)
    {
        return $this->locationRepository->getStateById($id);
    }

    public function createState(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->locationRepository->createState($data);
        });
    }

    public function updateState(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->locationRepository->updateState($id, $data);
        });
    }

    public function deleteState(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->locationRepository->deleteState($id);
        });
    }

    // Cities
    public function getAllCities(array $filters = [])
    {
        return $this->locationRepository->getAllCities($filters);
    }

    public function getCitiesByState(int $stateId, array $filters = [])
    {
        return $this->locationRepository->getCitiesByState($stateId, $filters);
    }

    public function getCityById(int $id)
    {
        return $this->locationRepository->getCityById($id);
    }

    public function createCity(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->locationRepository->createCity($data);
        });
    }

    public function updateCity(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->locationRepository->updateCity($id, $data);
        });
    }

    public function deleteCity(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->locationRepository->deleteCity($id);
        });
    }
}
