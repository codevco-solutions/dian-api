<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTerm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'name',
        'days',
        'discount_percentage',
        'discount_days',
        'is_default',
        'status',
        'notes'
    ];

    protected $casts = [
        'days' => 'integer',
        'discount_percentage' => 'float',
        'discount_days' => 'integer',
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
