<?php

namespace App\Models\Document;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class DocSequence extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'prefix',
        'next_number',
        'padding',
        'is_active'
    ];

    protected $casts = [
        'next_number' => 'integer',
        'padding' => 'integer',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function resolutions()
    {
        return $this->hasMany(DocResolution::class);
    }

    public function getNextNumber()
    {
        $number = $this->next_number;
        $this->increment('next_number');
        return str_pad($number, $this->padding, '0', STR_PAD_LEFT);
    }

    public function formatNumber($number)
    {
        return $this->prefix ? $this->prefix . $number : $number;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
