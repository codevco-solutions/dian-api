<?php

namespace App\Http\Resources\Document\Commercial;

use App\Http\Resources\MasterTable\TaxResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceTaxResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'invoice_item_id' => $this->invoice_item_id,
            'tax_id' => $this->tax_id,
            'taxable_amount' => $this->taxable_amount,
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->tax_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'invoice_item' => new InvoiceItemResource($this->whenLoaded('invoiceItem')),
            'tax' => new TaxResource($this->whenLoaded('tax'))
        ];
    }
}
