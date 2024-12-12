<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentReceiptDetail extends Model
{
    protected $fillable = [
        'payment_receipt_id',
        'document_type',
        'document_id',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function paymentReceipt()
    {
        return $this->belongsTo(PaymentReceipt::class);
    }

    public function document(): MorphTo
    {
        return $this->morphTo();
    }
}
