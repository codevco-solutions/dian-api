<?php

namespace App\Http\Resources\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'type' => $this->type,
            'position' => $this->position,
            'department' => $this->department,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'base_salary' => $this->base_salary,
            'payment_method' => $this->payment_method,
            'payment_frequency' => $this->payment_frequency,
            'working_hours_week' => $this->working_hours_week,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'employee' => new EmployeeResource($this->whenLoaded('employee'))
        ];
    }
}
