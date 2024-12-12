<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\Document\DianLogResource;
use App\Http\Resources\Document\DocLogResource;
use App\Http\Resources\Document\ErrorLogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollAdjustmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'payroll_document_id' => $this->payroll_document_id,
            'number' => $this->number,
            'prefix' => $this->prefix,
            'date' => $this->date,
            'total_earnings' => $this->total_earnings,
            'total_deductions' => $this->total_deductions,
            'net_adjustment' => $this->net_adjustment,
            'notes' => $this->notes,
            'status' => $this->status,
            'uuid' => $this->uuid,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'company' => new CompanyResource($this->whenLoaded('company')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'document' => new PayrollDocumentResource($this->whenLoaded('document')),
            'items' => PayrollAdjustmentItemResource::collection($this->whenLoaded('items')),
            'logs' => DocLogResource::collection($this->whenLoaded('logs')),
            'dian_logs' => DianLogResource::collection($this->whenLoaded('dianLogs')),
            'error_logs' => ErrorLogResource::collection($this->whenLoaded('errorLogs'))
        ];
    }
}
