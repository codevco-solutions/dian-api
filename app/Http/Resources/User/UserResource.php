<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company'),
            'branch_id' => $this->branch_id,
            'branch' => $this->whenLoaded('branch'),
            'role_id' => $this->role_id,
            'role' => $this->whenLoaded('role'),
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'settings' => $this->settings,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
