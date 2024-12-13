<?php

namespace App\Http\Resources\Company;

use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'commercial_name' => $this->commercial_name,
            'identification_type_id' => $this->identification_type_id,
            'identification_number' => $this->identification_number,
            'verification_code' => $this->verification_code,
            'organization_type_id' => $this->organization_type_id,
            'tax_regime_id' => $this->tax_regime_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'website' => $this->website,
            'subdomain' => $this->subdomain,
            'is_active' => $this->is_active,
            'branches' => BranchResource::collection($this->whenLoaded('branches')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
