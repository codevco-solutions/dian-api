<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'date',
        'amount',
        'payment_method',
        'reference',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            // Actualizar saldo de la factura
            $payment->invoice->calculateTotals();
        });

        static::deleted(function ($payment) {
            // Actualizar saldo de la factura
            $payment->invoice->calculateTotals();
        });
    }
}
