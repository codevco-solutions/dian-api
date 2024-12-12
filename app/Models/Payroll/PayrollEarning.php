<?php

namespace App\Models\Payroll;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class PayrollEarning extends Model
{
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'type',
        'calculation_type',
        'value',
        'percentage',
        'formula',
        'affects_social_security',
        'affects_parafiscal',
        'affects_retention',
        'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'percentage' => 'decimal:2',
        'affects_social_security' => 'boolean',
        'affects_parafiscal' => 'boolean',
        'affects_retention' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function documentEarnings()
    {
        return $this->hasMany(PayrollDocumentEarning::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function calculateAmount($baseValue, $quantity = 1)
    {
        switch ($this->calculation_type) {
            case 'fixed':
                return $this->value * $quantity;
            case 'percentage':
                return $baseValue * ($this->percentage / 100) * $quantity;
            case 'formula':
                // Aquí se debe implementar un evaluador de fórmulas
                return 0;
            default:
                return 0;
        }
    }
}
