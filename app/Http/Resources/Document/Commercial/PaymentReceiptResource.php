<?php

namespace App\Http\Resources\Document\Commercial;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Document\DocLogResource;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'partner_type' => $this->partner_type,
            'partner_id' => $this->partner_id,
            'number' => $this->number,
            'prefix' => $this->prefix,
            'date' => $this->date,
            'currency_code' => $this->currency_code,
            'exchange_rate' => $this->exchange_rate,
            'amount' => $this->amount,
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'reference' => $this->reference,
            'notes' => $this->notes,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'approved_at' => $this->approved_at ? $this->approved_at->format('Y-m-d H:i:s') : null,
            'cancelled_at' => $this->cancelled_at ? $this->cancelled_at->format('Y-m-d H:i:s') : null,
            'cancel_reason' => $this->cancel_reason,
            'can_edit' => $this->canEdit(),
            'can_delete' => $this->canDelete(),
            'can_approve' => $this->canApprove(),
            'can_cancel' => $this->canCancel(),

            // Relaciones
            'company' => new CompanyResource($this->whenLoaded('company')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'partner' => $this->whenLoaded('partner'),
            'details' => PaymentReceiptDetailResource::collection($this->whenLoaded('details')),
            'logs' => DocLogResource::collection($this->whenLoaded('logs'))
        ];
    }
}
