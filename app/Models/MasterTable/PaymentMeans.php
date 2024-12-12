<?php

namespace App\Models\MasterTable;

use Illuminate\Database\Eloquent\Model;

class PaymentMeans extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'group',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }
}
