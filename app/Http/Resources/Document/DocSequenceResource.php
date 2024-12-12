<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocSequenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'type' => $this->type,
            'prefix' => $this->prefix,
            'next_number' => $this->next_number,
            'padding' => $this->padding,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relaciones
            'resolutions' => DocResolutionResource::collection($this->whenLoaded('resolutions')),
            'company' => new CompanyResource($this->whenLoaded('company'))
        ];
    }
}
