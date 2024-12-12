<?php

namespace App\Models\Payroll;

use App\Models\Branch;
use App\Models\Company;
use App\Models\MasterTable\City;
use App\Models\MasterTable\Country;
use App\Models\MasterTable\IdentificationType;
use App\Models\MasterTable\State;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'identification_type_id',
        'identification_number',
        'first_name',
        'middle_name',
        'last_name',
        'second_last_name',
        'birth_date',
        'gender',
        'marital_status',
        'email',
        'phone',
        'mobile',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'postal_code',
        'bank_name',
        'bank_account_type',
        'bank_account_number',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function identificationType()
    {
        return $this->belongsTo(IdentificationType::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(EmployeeContract::class)->where('is_active', true);
    }

    public function payrollDocuments()
    {
        return $this->hasMany(PayrollDocument::class);
    }

    public function payrollAdjustments()
    {
        return $this->hasMany(PayrollAdjustment::class);
    }

    public function getFullNameAttribute()
    {
        return trim(implode(' ', [
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->second_last_name
        ]));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithActiveContract($query)
    {
        return $query->whereHas('contracts', function ($q) {
            $q->where('is_active', true);
        });
    }
}
