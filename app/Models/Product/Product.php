<?php

namespace App\Models\Product;

use App\Models\Company;
use App\Models\MasterTable\MeasurementUnit;
use App\Models\MasterTable\Tax;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'company_id',
        'category_id',
        'measurement_unit_id',
        'name',
        'code',
        'barcode',
        'description',
        'type',
        'base_price',
        'tax_rate',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'product_taxes')
            ->withPivot('rate')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProducts($query)
    {
        return $query->where('type', 'product');
    }

    public function scopeServices($query)
    {
        return $query->where('type', 'service');
    }
}
