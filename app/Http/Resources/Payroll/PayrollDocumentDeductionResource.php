<?php

namespace App\Http\Resources\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollDocumentDeductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payroll_document_id' => $this->payroll_document_id,
            'payroll_deduction_id' => $this->payroll_deduction_id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'document' => new PayrollDocumentResource($this->whenLoaded('document')),
            'deduction' => new PayrollDeductionResource($this->whenLoaded('deduction'))
        ];
    }
}
