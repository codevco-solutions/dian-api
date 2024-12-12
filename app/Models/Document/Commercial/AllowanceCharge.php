<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AllowanceCharge extends Model
{
    protected $table = 'allowance_charges';

    protected $fillable = [
        'chargeable_type',
        'chargeable_id',
        'charge_indicator',
        'allowance_charge_reason',
        'multiplier_factor_numeric',
        'base_amount',
        'amount',
        'base_amount_currency',
        'amount_currency',
    ];

    protected $casts = [
        'charge_indicator' => 'boolean',
        'multiplier_factor_numeric' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the parent chargeable model (Invoice, CreditNote, DebitNote).
     */
    public function chargeable(): MorphTo
    {
        return $this->morphTo();
    }
}
