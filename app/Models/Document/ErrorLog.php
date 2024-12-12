<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ErrorLog extends Model
{
    protected $fillable = [
        'type',
        'code',
        'message',
        'trace',
        'context'
    ];

    protected $casts = [
        'context' => 'json'
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithCode($query, $code)
    {
        return $query->where('code', $code);
    }
}
