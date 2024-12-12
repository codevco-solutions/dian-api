<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id',
        'price_list_id',
        'price',
        'discount_rate',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
