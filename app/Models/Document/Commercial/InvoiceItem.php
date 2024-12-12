<?php

namespace App\Models\Document\Commercial;

use App\Models\BaseModel;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'order_item_id',
        'product_id',
        'quantity',
        'price',
        'tax_rate',
        'discount_rate'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2'
    ];

    protected $appends = [
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total'
    ];

    // Relaciones
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Atributos calculados
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    public function getDiscountAmountAttribute()
    {
        return $this->subtotal * ($this->discount_rate / 100);
    }

    public function getTaxAmountAttribute()
    {
        $subtotalAfterDiscount = $this->subtotal - $this->discount_amount;
        return $subtotalAfterDiscount * ($this->tax_rate / 100);
    }

    public function getTotalAttribute()
    {
        return $this->subtotal - $this->discount_amount + $this->tax_amount;
    }
}
