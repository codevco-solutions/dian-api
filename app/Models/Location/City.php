<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Location\State;

class City extends Model
{
    protected $fillable = [
        'state_id',
        'name',
        'code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
