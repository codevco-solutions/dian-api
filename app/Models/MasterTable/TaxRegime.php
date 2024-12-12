<?php

namespace App\Models\MasterTable;

use Illuminate\Database\Eloquent\Model;

class TaxRegime extends Model
{
    protected $table = 'tax_regimes';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
