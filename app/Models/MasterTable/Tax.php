<?php

namespace App\Models\MasterTable;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'default_rate',
        'type',
        'is_retention',
        'is_active'
    ];

    protected $casts = [
        'default_rate' => 'decimal:2',
        'is_retention' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
