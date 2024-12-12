<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PayrollAdjustmentItem extends Model
{
    protected $fillable = [
        'payroll_adjustment_id',
        'concept_type',
        'concept_id',
        'original_amount',
        'adjustment_amount',
        'final_amount',
        'notes'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'final_amount' => 'decimal:2'
    ];

    public function adjustment()
    {
        return $this->belongsTo(PayrollAdjustment::class, 'payroll_adjustment_id');
    }

    public function concept(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            // Recalcular totales del ajuste
            $item->adjustment->calculateTotals();
        });
    }
}
