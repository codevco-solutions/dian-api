<?php

namespace App\Models\MasterTable;

use Illuminate\Database\Eloquent\Model;

class OperationType extends Model
{
    protected $table = 'operation_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'prefix',
        'group'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
