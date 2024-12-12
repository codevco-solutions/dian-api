<?php

namespace App\Http\Resources\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollAdjustmentItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payroll_adjustment_id' => $this->payroll_adjustment_id,
            'concept_type' => $this->concept_type,
            'concept_id' => $this->concept_id,
            'original_amount' => $this->original_amount,
            'adjustment_amount' => $this->adjustment_amount,
            'final_amount' => $this->final_amount,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'adjustment' => new PayrollAdjustmentResource($this->whenLoaded('adjustment')),
            'concept' => $this->whenLoaded('concept')
        ];
    }
}
