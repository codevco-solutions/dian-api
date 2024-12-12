<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaxWithholding extends Model
{
    protected $table = 'tax_withholdings';

    protected $fillable = [
        'withholdable_type',
        'withholdable_id',
        'tax_category',
        'tax_scheme_id',
        'tax_scheme_name',
        'tax_scheme_code',
        'taxable_amount',
        'taxable_amount_currency',
        'tax_amount',
        'tax_amount_currency',
        'percent',
    ];

    protected $casts = [
        'taxable_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'percent' => 'decimal:2',
    ];

    /**
     * Get the parent withholdable model (Invoice, CreditNote, DebitNote).
     */
    public function withholdable(): MorphTo
    {
        return $this->morphTo();
    }
}
