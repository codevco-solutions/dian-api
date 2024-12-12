<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MasterTable\IdentificationType;
use App\Models\MasterTable\TaxRegime;
use App\Models\MasterTable\OrganizationType;
use App\Models\Branch\Branch;
use App\Models\Auth\User;

class Company extends Model
{
    protected $fillable = [
        'identification_type_id',
        'tax_regime_id',
        'organization_type_id',
        'identification_number',
        'verification_code',
        'name',
        'commercial_name',
        'email',
        'phone',
        'address',
        'website',
        'logo',
        'subdomain',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function identificationType(): BelongsTo
    {
        return $this->belongsTo(IdentificationType::class);
    }

    public function taxRegime(): BelongsTo
    {
        return $this->belongsTo(TaxRegime::class);
    }

    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
