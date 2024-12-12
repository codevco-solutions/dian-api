<?php

namespace App\Models\Document\Commercial;

use App\Models\MasterTable\MeasurementUnit;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'quote_item_id',
        'product_id',
        'code',
        'name',
        'description',
        'quantity',
        'delivered_quantity',
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
        'delivered_quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'json'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function quoteItem()
    {
        return $this->belongsTo(QuoteItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        $this->discount_amount = $this->subtotal * ($this->discount_rate / 100);
        // El tax_amount se debe calcular según las reglas de impuestos del producto
        $this->total = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();

        // Recalcular totales de la orden
        $this->order->calculateTotals();
    }

    public function getPendingQuantity()
    {
        return $this->quantity - $this->delivered_quantity;
    }

    public function updateDeliveryStatus($deliveredQuantity)
    {
        $this->delivered_quantity += $deliveredQuantity;
        $this->save();

        // Actualizar estado de la orden
        $this->order->updateDeliveryStatus();
    }
}
