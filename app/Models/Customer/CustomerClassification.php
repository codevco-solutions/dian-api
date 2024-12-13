<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerClassification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'criteria',
        'min_purchase_amount',
        'min_purchase_frequency',
        'payment_behavior_score',
        'credit_score',
        'status',
        'color',
        'icon'
    ];

    protected $casts = [
        'criteria' => 'array',
        'min_purchase_amount' => 'float',
        'min_purchase_frequency' => 'integer',
        'payment_behavior_score' => 'float',
        'credit_score' => 'float'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'classification_id');
    }
}
