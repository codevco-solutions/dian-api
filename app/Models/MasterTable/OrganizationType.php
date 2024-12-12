<?php

namespace App\Models\MasterTable;

use Illuminate\Database\Eloquent\Model;

class OrganizationType extends Model
{
    protected $table = 'organization_types';

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
