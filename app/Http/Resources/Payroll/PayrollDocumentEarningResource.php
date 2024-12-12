<?php

namespace App\Http\Resources\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollDocumentEarningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payroll_document_id' => $this->payroll_document_id,
            'payroll_earning_id' => $this->payroll_earning_id,
            'quantity' => $this->quantity,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'document' => new PayrollDocumentResource($this->whenLoaded('document')),
            'earning' => new PayrollEarningResource($this->whenLoaded('earning'))
        ];
    }
}
