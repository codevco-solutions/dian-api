<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Model;

class PayrollDocumentDeduction extends Model
{
    protected $fillable = [
        'payroll_document_id',
        'payroll_deduction_id',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function document()
    {
        return $this->belongsTo(PayrollDocument::class, 'payroll_document_id');
    }

    public function deduction()
    {
        return $this->belongsTo(PayrollDeduction::class, 'payroll_deduction_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($documentDeduction) {
            // Recalcular totales del documento
            $documentDeduction->document->calculateTotals();
        });
    }
}
