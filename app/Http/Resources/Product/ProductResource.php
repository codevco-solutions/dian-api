<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\MasterTable\MeasurementUnitResource;
use App\Http\Resources\MasterTable\TaxResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'measurement_unit' => new MeasurementUnitResource($this->whenLoaded('measurementUnit')),
            'name' => $this->name,
            'code' => $this->code,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'type' => $this->type,
            'base_price' => $this->base_price,
            'tax_rate' => $this->tax_rate,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'prices' => ProductPriceResource::collection($this->whenLoaded('prices')),
            'taxes' => TaxResource::collection($this->whenLoaded('taxes'))
        ];
    }
}
