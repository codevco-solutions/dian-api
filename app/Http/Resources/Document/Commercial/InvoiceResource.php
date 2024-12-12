namespace App\Http\Resources\Document\Commercial;

use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'order' => new OrderResource($this->whenLoaded('order')),
            'number' => $this->number,
            'prefix' => $this->prefix,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate,
            'notes' => $this->notes,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'balance' => $this->balance,
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'taxes' => InvoiceTaxResource::collection($this->whenLoaded('taxes')),
            'payments' => InvoicePaymentResource::collection($this->whenLoaded('payments')),
            'credit_notes' => CreditNoteResource::collection($this->whenLoaded('creditNotes')),
            'debit_notes' => DebitNoteResource::collection($this->whenLoaded('debitNotes')),
            'dian_state' => $this->dian_state,
            'dian_response' => $this->dian_response,
            'dian_errors' => $this->dian_errors,
            'dian_xml_filename' => $this->dian_xml_filename,
            'dian_pdf_filename' => $this->dian_pdf_filename,
            'dian_zip_filename' => $this->dian_zip_filename,
            'dian_track_id' => $this->dian_track_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'approved_at' => $this->approved_at ? $this->approved_at->format('Y-m-d H:i:s') : null,
            'cancelled_at' => $this->cancelled_at ? $this->cancelled_at->format('Y-m-d H:i:s') : null,
            'cancel_reason' => $this->cancel_reason,
            'can_edit' => $this->canEdit(),
            'can_delete' => $this->canDelete(),
            'can_approve' => $this->canApprove(),
            'can_cancel' => $this->canCancel(),
            'can_create_credit_note' => $this->canCreateCreditNote(),
            'can_create_debit_note' => $this->canCreateDebitNote(),
        ];
    }
}
