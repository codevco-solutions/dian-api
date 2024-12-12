<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DianLog extends Model
{
    protected $fillable = [
        'type',
        'status',
        'request',
        'response',
        'tracking_id'
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

    public function scopeWithTrackingId($query, $trackingId)
    {
        return $query->where('tracking_id', $trackingId);
    }
}
