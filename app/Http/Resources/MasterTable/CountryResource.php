<?php

namespace App\Http\Resources\MasterTable;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code_2' => $this->code_2,
            'code_3' => $this->code_3,
            'numeric_code' => $this->numeric_code,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'states_count' => $this->when($request->has('with_counts'), function () {
                return $this->states()->count();
            }),
            'states' => StateResource::collection($this->whenLoaded('states')),
        ];
    }
}
