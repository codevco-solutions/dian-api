<?php

namespace App\Http\Resources\Document\Commercial;

use App\Http\Resources\MasterTable\MeasurementUnitResource;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'quote_item_id' => $this->quote_item_id,
            'product_id' => $this->product_id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'delivered_quantity' => $this->delivered_quantity,
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
            'order' => new OrderResource($this->whenLoaded('order')),
            'quote_item' => new QuoteItemResource($this->whenLoaded('quoteItem')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'measurement_unit' => new MeasurementUnitResource($this->whenLoaded('measurementUnit')),
            'invoice_items' => InvoiceItemResource::collection($this->whenLoaded('invoiceItems'))
        ];
    }
}