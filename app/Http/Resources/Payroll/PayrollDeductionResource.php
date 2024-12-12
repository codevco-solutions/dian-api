<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\Company\CompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollDeductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'calculation_type' => $this->calculation_type,
            'value' => $this->value,
            'percentage' => $this->percentage,
            'formula' => $this->formula,
            'is_mandatory' => $this->is_mandatory,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'company' => new CompanyResource($this->whenLoaded('company')),
            'document_deductions' => PayrollDocumentDeductionResource::collection($this->whenLoaded('documentDeductions'))
        ];
    }
}
