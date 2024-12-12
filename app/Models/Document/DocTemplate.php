<?php

namespace App\Models\Document;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class DocTemplate extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'name',
        'header',
        'footer',
        'body',
        'styles',
        'is_default',
        'is_active'
    ];

    protected $casts = [
        'styles' => 'json',
        'is_default' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
