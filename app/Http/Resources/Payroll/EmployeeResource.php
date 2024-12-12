<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\MasterTable\CityResource;
use App\Http\Resources\MasterTable\CountryResource;
use App\Http\Resources\MasterTable\IdentificationTypeResource;
use App\Http\Resources\MasterTable\StateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'identification_type_id' => $this->identification_type_id,
            'identification_number' => $this->identification_number,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'second_last_name' => $this->second_last_name,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'postal_code' => $this->postal_code,
            'bank_name' => $this->bank_name,
            'bank_account_type' => $this->bank_account_type,
            'bank_account_number' => $this->bank_account_number,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Atributos calculados
            'full_name' => $this->full_name,

            // Relaciones
            'company' => new CompanyResource($this->whenLoaded('company')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'identification_type' => new IdentificationTypeResource($this->whenLoaded('identificationType')),
            'country' => new CountryResource($this->whenLoaded('country')),
            'state' => new StateResource($this->whenLoaded('state')),
            'city' => new CityResource($this->whenLoaded('city')),
            'contracts' => EmployeeContractResource::collection($this->whenLoaded('contracts')),
            'active_contract' => new EmployeeContractResource($this->whenLoaded('activeContract')),
            'payroll_documents' => PayrollDocumentResource::collection($this->whenLoaded('payrollDocuments')),
            'payroll_adjustments' => PayrollAdjustmentResource::collection($this->whenLoaded('payrollAdjustments'))
        ];
    }
}
