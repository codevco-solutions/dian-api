<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocResolutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'doc_sequence_id' => $this->doc_sequence_id,
            'resolution_number' => $this->resolution_number,
            'type' => $this->type,
            'resolution_date' => $this->resolution_date,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'prefix' => $this->prefix,
            'start_number' => $this->start_number,
            'end_number' => $this->end_number,
            'technical_key' => $this->technical_key,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'sequence' => new DocSequenceResource($this->whenLoaded('sequence')),
            'company' => new CompanyResource($this->whenLoaded('company'))
        ];
    }
}
