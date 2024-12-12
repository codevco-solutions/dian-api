<?php

namespace App\Http\Resources\Common;

use App\Http\Resources\MasterTable\CityResource;
use App\Http\Resources\MasterTable\CountryResource;
use App\Http\Resources\MasterTable\StateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country' => new CountryResource($this->whenLoaded('country')),
            'state' => new StateResource($this->whenLoaded('state')),
            'city' => new CityResource($this->whenLoaded('city')),
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'contact_person' => $this->contact_person,
            'is_main' => $this->is_main,
            'is_billing' => $this->is_billing,
            'is_shipping' => $this->is_shipping,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
