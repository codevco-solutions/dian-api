<?php

namespace App\Http\Resources\Document\Commercial;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Document\DianLogResource;
use App\Http\Resources\Document\DocLogResource;
use App\Http\Resources\Document\ErrorLogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'customer_id' => $this->customer_id,
            'invoice_id' => $this->invoice_id,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'number' => $this->number,
            'prefix' => $this->prefix,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'currency_code' => $this->currency_code,
            'exchange_rate' => $this->exchange_rate,
            'notes' => $this->notes,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'items' => CreditNoteItemResource::collection($this->whenLoaded('items')),
            'dian_state' => $this->dian_state,
            'dian_response' => $this->dian_response,
            'dian_errors' => $this->dian_errors,
            'dian_xml_filename' => $this->dian_xml_filename,
            'dian_pdf_filename' => $this->dian_pdf_filename,
            'dian_zip_filename' => $this->dian_zip_filename,
            'dian_track_id' => $this->dian_track_id,
            'uuid' => $this->uuid,
            'qr_data' => $this->qr_data,
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
            'logs' => DocLogResource::collection($this->whenLoaded('logs')),
            'dian_logs' => DianLogResource::collection($this->whenLoaded('dianLogs')),
            'error_logs' => ErrorLogResource::collection($this->whenLoaded('errorLogs'))
        ];
    }
}
