<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Location\State;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code_2',
        'code_3',
        'numeric_code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
