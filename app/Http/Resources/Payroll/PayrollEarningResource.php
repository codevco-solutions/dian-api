<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\Company\CompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollEarningResource extends JsonResource
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
            'affects_social_security' => $this->affects_social_security,
            'affects_parafiscal' => $this->affects_parafiscal,
            'affects_retention' => $this->affects_retention,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'company' => new CompanyResource($this->whenLoaded('company')),
            'document_earnings' => PayrollDocumentEarningResource::collection($this->whenLoaded('documentEarnings'))
        ];
    }
}
