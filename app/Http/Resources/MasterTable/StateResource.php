<?php

namespace App\Http\Resources\MasterTable;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'name' => $this->name,
            'code' => $this->code,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'country' => new CountryResource($this->whenLoaded('country')),
            'cities_count' => $this->when($request->has('with_counts'), function () {
                return $this->cities()->count();
            }),
            'cities' => CityResource::collection($this->whenLoaded('cities')),
        ];
    }
}
