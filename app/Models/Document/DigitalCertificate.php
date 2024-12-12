<?php

namespace App\Models\Document;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class DigitalCertificate extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'certificate',
        'password',
        'valid_from',
        'valid_to',
        'pin',
        'software_id',
        'is_active'
    ];

    protected $hidden = [
        'certificate',
        'password',
        'pin'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isValid()
    {
        $now = now();
        return $this->is_active &&
            $now->greaterThanOrEqualTo($this->valid_from) &&
            $now->lessThanOrEqualTo($this->valid_to);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where('valid_from', '<=', $now)
            ->where('valid_to', '>=', $now);
    }
}
