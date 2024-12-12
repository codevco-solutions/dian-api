<?php

namespace App\Repositories\Eloquent\MasterTable;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Repositories\Contracts\MasterTable\LocationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LocationRepository implements LocationRepositoryInterface
{
    // Countries
    public function getAllCountries(array $filters = []): LengthAwarePaginator
    {
        $query = Country::query();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('code_2', 'like', "%{$filters['search']}%")
                    ->orWhere('code_3', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function getCountryById(int $id): ?Country
    {
        return Country::find($id);
    }

    public function createCountry(array $data): Country
    {
        return Country::create($data);
    }

    public function updateCountry(int $id, array $data): bool
    {
        $country = Country::find($id);
        if (!$country) return false;
        return $country->update($data);
    }

    public function deleteCountry(int $id): bool
    {
        $country = Country::find($id);
        if (!$country) return false;
        return $country->delete();
    }

    // States
    public function getAllStates(array $filters = []): LengthAwarePaginator
    {
        $query = State::query();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('code', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function getStatesByCountry(int $countryId, array $filters = []): LengthAwarePaginator
    {
        $query = State::where('country_id', $countryId);

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('code', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function getStateById(int $id): ?State
    {
        return State::find($id);
    }

    public function createState(array $data): State
    {
        return State::create($data);
    }

    public function updateState(int $id, array $data): bool
    {
        $state = State::find($id);
        if (!$state) return false;
        return $state->update($data);
    }

    public function deleteState(int $id): bool
    {
        $state = State::find($id);
        if (!$state) return false;
        return $state->delete();
    }

    // Cities
    public function getAllCities(array $filters = []): LengthAwarePaginator
    {
        $query = City::query();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('code', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['state_id'])) {
            $query->where('state_id', $filters['state_id']);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function getCitiesByState(int $stateId, array $filters = []): LengthAwarePaginator
    {
        $query = City::where('state_id', $stateId);

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('code', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function getCityById(int $id): ?City
    {
        return City::find($id);
    }

    public function createCity(array $data): City
    {
        return City::create($data);
    }

    public function updateCity(int $id, array $data): bool
    {
        $city = City::find($id);
        if (!$city) return false;
        return $city->update($data);
    }

    public function deleteCity(int $id): bool
    {
        $city = City::find($id);
        if (!$city) return false;
        return $city->delete();
    }
}
