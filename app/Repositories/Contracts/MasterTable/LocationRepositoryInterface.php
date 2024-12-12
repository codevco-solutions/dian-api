<?php

namespace App\Repositories\Contracts\MasterTable;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Pagination\LengthAwarePaginator;

interface LocationRepositoryInterface
{
    // Countries
    public function getAllCountries(array $filters = []): LengthAwarePaginator;
    public function getCountryById(int $id): ?Country;
    public function createCountry(array $data): Country;
    public function updateCountry(int $id, array $data): bool;
    public function deleteCountry(int $id): bool;
    
    // States
    public function getAllStates(array $filters = []): LengthAwarePaginator;
    public function getStatesByCountry(int $countryId, array $filters = []): LengthAwarePaginator;
    public function getStateById(int $id): ?State;
    public function createState(array $data): State;
    public function updateState(int $id, array $data): bool;
    public function deleteState(int $id): bool;
    
    // Cities
    public function getAllCities(array $filters = []): LengthAwarePaginator;
    public function getCitiesByState(int $stateId, array $filters = []): LengthAwarePaginator;
    public function getCityById(int $id): ?City;
    public function createCity(array $data): City;
    public function updateCity(int $id, array $data): bool;
    public function deleteCity(int $id): bool;
}
