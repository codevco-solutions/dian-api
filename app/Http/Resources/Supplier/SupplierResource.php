<?php

namespace App\Http\Resources\Supplier;

use App\Http\Resources\Common\AddressResource;
use App\Http\Resources\Common\ContactResource;
use App\Http\Resources\MasterTable\IdentificationTypeResource;
use App\Http\Resources\MasterTable\TaxRegimeResource;
use App\Http\Resources\MasterTable\TaxResponsibilityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'identification_type' => new IdentificationTypeResource($this->whenLoaded('identificationType')),
            'tax_regime' => new TaxRegimeResource($this->whenLoaded('taxRegime')),
            'identification_number' => $this->identification_number,
            'verification_digit' => $this->verification_digit,
            'name' => $this->name,
            'commercial_name' => $this->commercial_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'website' => $this->website,
            'notes' => $this->notes,
            'credit_limit' => $this->credit_limit,
            'payment_term_days' => $this->payment_term_days,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'tax_responsibilities' => TaxResponsibilityResource::collection($this->whenLoaded('taxResponsibilities')),
            'main_address' => new AddressResource($this->whenLoaded('mainAddress')),
            'billing_address' => new AddressResource($this->whenLoaded('billingAddress')),
            'shipping_address' => new AddressResource($this->whenLoaded('shippingAddress')),
            'primary_contact' => new ContactResource($this->whenLoaded('primaryContact'))
        ];
    }
}
