<?php

namespace App\Http\Resources\Document\Commercial;

use App\Http\Resources\MasterTable\MeasurementUnitResource;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quote_id' => $this->quote_id,
            'product_id' => $this->product_id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'measurement_unit_id' => $this->measurement_unit_id,
            'unit_price' => $this->unit_price,
            'discount_rate' => $this->discount_rate,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'quote' => new QuoteResource($this->whenLoaded('quote')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'measurement_unit' => new MeasurementUnitResource($this->whenLoaded('measurementUnit'))
        ];
    }
}
