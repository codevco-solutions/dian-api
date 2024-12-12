<?php

namespace App\Models\Supplier;

use App\Models\Common\Address;
use App\Models\Common\Contact;
use App\Models\Company;
use App\Models\MasterTable\IdentificationType;
use App\Models\MasterTable\TaxRegime;
use App\Models\MasterTable\TaxResponsibility;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'company_id',
        'identification_type_id',
        'tax_regime_id',
        'identification_number',
        'verification_digit',
        'name',
        'commercial_name',
        'email',
        'phone',
        'mobile',
        'website',
        'notes',
        'credit_limit',
        'payment_term_days',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_term_days' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function identificationType()
    {
        return $this->belongsTo(IdentificationType::class);
    }

    public function taxRegime()
    {
        return $this->belongsTo(TaxRegime::class);
    }

    public function taxResponsibilities()
    {
        return $this->belongsToMany(TaxResponsibility::class, 'supplier_tax_responsibilities')
            ->withTimestamps();
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function mainAddress()
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('is_main', true);
    }

    public function billingAddress()
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('is_billing', true);
    }

    public function shippingAddress()
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('is_shipping', true);
    }

    public function primaryContact()
    {
        return $this->morphOne(Contact::class, 'contactable')
            ->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
