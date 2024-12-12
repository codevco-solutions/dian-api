<?php

namespace App\Models\Document\Commercial;

use App\Models\MasterTable\MeasurementUnit;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    protected $fillable = [
        'credit_note_id',
        'invoice_item_id',
        'product_id',
        'code',
        'name',
        'description',
        'quantity',
        'measurement_unit_id',
        'unit_price',
        'tax_amount',
        'subtotal',
        'total',
        'metadata'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'json'
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        // El tax_amount se debe calcular según las reglas de impuestos del producto
        $this->total = $this->subtotal + $this->tax_amount;
        $this->save();

        // Recalcular totales de la nota crédito
        $this->creditNote->calculateTotals();
    }
}
