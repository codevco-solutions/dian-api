<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMeans extends Model
{
    protected $table = 'payment_means';

    protected $fillable = [
        'invoice_id',
        'payment_means_code',
        'payment_means_description',
        'payment_due_date',
        'payment_id',
        'payment_method_code',
        'payment_method_description',
        'payment_terms',
    ];

    protected $casts = [
        'payment_due_date' => 'datetime',
    ];

    /**
     * Get the invoice that owns the payment means.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
