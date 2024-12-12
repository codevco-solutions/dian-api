<?php

namespace App\Http\Resources\Document\Commercial;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\Document\DocLogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'partner_type' => $this->partner_type,
            'partner_id' => $this->partner_id,
            'type' => $this->type,
            'number' => $this->number,
            'quote_id' => $this->quote_id,
            'date' => $this->date,
            'delivery_date' => $this->delivery_date,
            'currency_code' => $this->currency_code,
            'exchange_rate' => $this->exchange_rate,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'notes' => $this->notes,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'company' => new CompanyResource($this->whenLoaded('company')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'partner' => $this->whenLoaded('partner'),
            'quote' => new QuoteResource($this->whenLoaded('quote')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'logs' => DocLogResource::collection($this->whenLoaded('logs')),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices'))
        ];
    }
}
