<?php

namespace App\Http\Resources\Document\Commercial;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentReceiptDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_receipt_id' => $this->payment_receipt_id,
            'document_type' => $this->document_type,
            'document_id' => $this->document_id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relaciones
            'payment_receipt' => new PaymentReceiptResource($this->whenLoaded('paymentReceipt')),
            'document' => $this->whenLoaded('document')
        ];
    }
}
