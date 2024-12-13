<?php

namespace App\Models\Branch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company\Company;
use App\Models\Auth\User;

class Branch extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'postal_code',
        'phone',
        'email',
        'is_main',
        'is_active'
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
