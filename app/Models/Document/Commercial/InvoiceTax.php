<?php

namespace App\Models\Document\Commercial;

use App\Models\MasterTable\Tax;
use Illuminate\Database\Eloquent\Model;

class InvoiceTax extends Model
{
    protected $fillable = [
        'invoice_id',
        'invoice_item_id',
        'tax_id',
        'taxable_amount',
        'tax_rate',
        'tax_amount'
    ];

    protected $casts = [
        'taxable_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function calculateTax()
    {
        $this->tax_amount = $this->taxable_amount * ($this->tax_rate / 100);
        $this->save();

        // Si pertenece a un item, recalcular sus totales
        if ($this->invoice_item_id) {
            $this->invoiceItem->calculateTotals();
        } else {
            // Si es un impuesto general de la factura
            $this->invoice->calculateTotals();
        }
    }
}
