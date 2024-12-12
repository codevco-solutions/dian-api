<?php

namespace App\Models\Document\Commercial;

use App\Models\MasterTable\MeasurementUnit;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'product_id',
        'code',
        'name',
        'description',
        'quantity',
        'measurement_unit_id',
        'unit_price',
        'discount_rate',
        'discount_amount',
        'tax_amount',
        'subtotal',
        'total',
        'metadata'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'json'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
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
        $this->discount_amount = $this->subtotal * ($this->discount_rate / 100);
        // El tax_amount se debe calcular según las reglas de impuestos del producto
        $this->total = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();

        // Recalcular totales de la cotización
        $this->quote->calculateTotals();
    }
}
