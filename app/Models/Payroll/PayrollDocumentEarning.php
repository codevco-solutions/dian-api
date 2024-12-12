<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Model;

class PayrollDocumentEarning extends Model
{
    protected $fillable = [
        'payroll_document_id',
        'payroll_earning_id',
        'quantity',
        'rate',
        'amount'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    public function document()
    {
        return $this->belongsTo(PayrollDocument::class, 'payroll_document_id');
    }

    public function earning()
    {
        return $this->belongsTo(PayrollEarning::class, 'payroll_earning_id');
    }

    public function calculateAmount()
    {
        $this->amount = $this->quantity * $this->rate;
        $this->save();

        // Recalcular totales del documento
        $this->document->calculateTotals();
    }
}
