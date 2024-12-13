<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocumentChange extends Model
{
    protected $fillable = [
        'action',
        'user_id',
        'changes',
        'data',
        'metadata'
    ];

    protected $casts = [
        'changes' => 'array',
        'data' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Obtener el modelo que registró el cambio
     */
    public function changeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Obtener el usuario que realizó el cambio
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
