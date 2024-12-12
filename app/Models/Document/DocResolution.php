<?php

namespace App\Models\Document;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class DocResolution extends Model
{
    protected $fillable = [
        'company_id',
        'doc_sequence_id',
        'resolution_number',
        'type',
        'resolution_date',
        'start_date',
        'end_date',
        'prefix',
        'start_number',
        'end_number',
        'technical_key',
        'is_active'
    ];

    protected $casts = [
        'resolution_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'start_number' => 'integer',
        'end_number' => 'integer',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sequence()
    {
        return $this->belongsTo(DocSequence::class, 'doc_sequence_id');
    }

    public function isValid()
    {
        $now = now();
        return $this->is_active &&
            $now->greaterThanOrEqualTo($this->start_date) &&
            $now->lessThanOrEqualTo($this->end_date);
    }

    public function isNumberInRange($number)
    {
        return $number >= $this->start_number && $number <= $this->end_number;
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
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now);
    }
}
