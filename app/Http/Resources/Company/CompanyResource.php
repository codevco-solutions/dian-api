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
            'business_name' => $this->business_name,
            'trade_name' => $this->trade_name,
            'tax_id' => $this->tax_id,
            'tax_regime' => $this->tax_regime,
            'economic_activity' => $this->economic_activity,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'website' => $this->website,
            'subdomain' => $this->subdomain,
            'branches' => BranchResource::collection($this->whenLoaded('branches')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
