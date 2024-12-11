<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_name',
        'trade_name',
        'tax_id',
        'tax_regime',
        'economic_activity',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'subdomain',
        'is_active',
        'dian_settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'dian_settings' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($company) {
            // Cuando se elimina una compañía, todas sus sucursales pasan a is_main = false
            $company->branches()->update([
                'is_main' => false,
                'is_active' => false
            ]);
            
            $company->is_active = false;
            $company->save();
        });

        static::restoring(function ($company) {
            // Cuando se restaura una compañía, todas sus sucursales vuelven a is_main = true
            $company->branches()->update([
                'is_main' => true,
                'is_active' => true
            ]);
            
            $company->is_active = true;
            $company->save();
        });
    }

    /**
     * Get the branches for the company.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get the users for the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function mainBranch()
    {
        return $this->hasOne(Branch::class)->where('is_main', true);
    }
}
