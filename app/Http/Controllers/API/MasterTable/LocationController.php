<?php

namespace App\Http\Controllers\API\MasterTable;

use App\Http\Controllers\Controller;
use App\Http\Resources\MasterTable\CityResource;
use App\Http\Resources\MasterTable\CountryResource;
use App\Http\Resources\MasterTable\StateResource;
use App\Services\MasterTable\LocationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    // Countries
    public function indexCountries(Request $request)
    {
        $filters = $request->only(['search', 'is_active', 'per_page']);
        $countries = $this->locationService->getAllCountries($filters);
        
        return CountryResource::collection($countries);
    }

    public function showCountry($id)
    {
        $country = $this->locationService->getCountryById($id);
        
        if (!$country) {
            return $this->respondNotFound('Country not found');
        }
        
        return new CountryResource($country);
    }

    public function storeCountry(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code_2' => 'required|string|size:2|unique:countries',
            'code_3' => 'required|string|size:3|unique:countries',
            'numeric_code' => 'required|string|unique:countries',
            'is_active' => 'boolean'
        ]);

        $country = $this->locationService->createCountry($validatedData);
        
        return new CountryResource($country);
    }

    public function updateCountry(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'code_2' => 'string|size:2|unique:countries,code_2,' . $id,
            'code_3' => 'string|size:3|unique:countries,code_3,' . $id,
            'numeric_code' => 'string|unique:countries,numeric_code,' . $id,
            'is_active' => 'boolean'
        ]);

        $updated = $this->locationService->updateCountry($id, $validatedData);
        
        if (!$updated) {
            return $this->respondNotFound('Country not found');
        }
        
        return $this->respondSuccess('Country updated successfully');
    }

    public function destroyCountry($id)
    {
        $deleted = $this->locationService->deleteCountry($id);
        
        if (!$deleted) {
            return $this->respondNotFound('Country not found');
        }
        
        return $this->respondSuccess('Country deleted successfully');
    }

    // States
    public function indexStates(Request $request)
    {
        $filters = $request->only(['search', 'is_active', 'per_page', 'country_id']);
        $states = $this->locationService->getAllStates($filters);
        
        return StateResource::collection($states);
    }

    public function showState($id)
    {
        $state = $this->locationService->getStateById($id);
        
        if (!$state) {
            return $this->respondNotFound('State not found');
        }
        
        return new StateResource($state);
    }

    public function statesByCountry($countryId)
    {
        $states = $this->locationService->getStatesByCountry($countryId);
        return StateResource::collection($states);
    }

    public function storeState(Request $request)
    {
        $validatedData = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:states,code,NULL,id,country_id,' . $request->country_id,
            'is_active' => 'boolean'
        ]);

        $state = $this->locationService->createState($validatedData);
        
        return new StateResource($state);
    }

    public function updateState(Request $request, $id)
    {
        $state = $this->locationService->getStateById($id);
        
        if (!$state) {
            return $this->respondNotFound('State not found');
        }

        $validatedData = $request->validate([
            'country_id' => 'exists:countries,id',
            'name' => 'string|max:255',
            'code' => 'string|unique:states,code,' . $id . ',id,country_id,' . ($request->country_id ?? $state->country_id),
            'is_active' => 'boolean'
        ]);

        $updated = $this->locationService->updateState($id, $validatedData);
        
        return $this->respondSuccess('State updated successfully');
    }

    public function destroyState($id)
    {
        $deleted = $this->locationService->deleteState($id);
        
        if (!$deleted) {
            return $this->respondNotFound('State not found');
        }
        
        return $this->respondSuccess('State deleted successfully');
    }

    // Cities
    public function indexCities(Request $request)
    {
        $filters = $request->only(['search', 'is_active', 'per_page', 'state_id']);
        $cities = $this->locationService->getAllCities($filters);
        
        return CityResource::collection($cities);
    }

    public function showCity($id)
    {
        $city = $this->locationService->getCityById($id);
        
        if (!$city) {
            return $this->respondNotFound('City not found');
        }
        
        return new CityResource($city);
    }

    public function citiesByState($stateId)
    {
        $cities = $this->locationService->getCitiesByState($stateId);
        return CityResource::collection($cities);
    }

    public function storeCity(Request $request)
    {
        $validatedData = $request->validate([
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:cities,code,NULL,id,state_id,' . $request->state_id,
            'is_active' => 'boolean'
        ]);

        $city = $this->locationService->createCity($validatedData);
        
        return new CityResource($city);
    }

    public function updateCity(Request $request, $id)
    {
        $city = $this->locationService->getCityById($id);
        
        if (!$city) {
            return $this->respondNotFound('City not found');
        }

        $validatedData = $request->validate([
            'state_id' => 'exists:states,id',
            'name' => 'string|max:255',
            'code' => 'string|unique:cities,code,' . $id . ',id,state_id,' . ($request->state_id ?? $city->state_id),
            'is_active' => 'boolean'
        ]);

        $updated = $this->locationService->updateCity($id, $validatedData);
        
        return $this->respondSuccess('City updated successfully');
    }

    public function destroyCity($id)
    {
        $deleted = $this->locationService->deleteCity($id);
        
        if (!$deleted) {
            return $this->respondNotFound('City not found');
        }
        
        return $this->respondSuccess('City deleted successfully');
    }
}
