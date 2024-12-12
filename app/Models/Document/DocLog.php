<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocLog extends Model
{
    protected $fillable = [
        'type',
        'status',
        'message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'json'
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
