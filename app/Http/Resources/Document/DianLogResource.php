<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DianLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,
            'type' => $this->type,
            'status' => $this->status,
            'request' => $this->request,
            'response' => $this->response,
            'tracking_id' => $this->tracking_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'documentable' => $this->whenLoaded('documentable')
        ];
    }
}
