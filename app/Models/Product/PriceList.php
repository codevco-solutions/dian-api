<?php

namespace App\Models\Product;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'is_default',
        'is_active'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
