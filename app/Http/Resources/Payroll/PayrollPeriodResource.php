<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\Company\CompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'type' => $this->type,
            'year' => $this->year,
            'month' => $this->month,
            'period' => $this->period,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'payment_date' => $this->payment_date,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Atributos calculados
            'name' => $this->name,
            'days_in_period' => $this->getDaysInPeriod(),

            // Relaciones
            'company' => new CompanyResource($this->whenLoaded('company')),
            'payroll_documents' => PayrollDocumentResource::collection($this->whenLoaded('payrollDocuments'))
        ];
    }
}
