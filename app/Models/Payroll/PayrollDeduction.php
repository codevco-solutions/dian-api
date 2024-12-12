<?php

namespace App\Models\Payroll;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class PayrollDeduction extends Model
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
        'is_mandatory',
        'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function documentDeductions()
    {
        return $this->hasMany(PayrollDocumentDeduction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function calculateAmount($baseValue)
    {
        switch ($this->calculation_type) {
            case 'fixed':
                return $this->value;
            case 'percentage':
                return $baseValue * ($this->percentage / 100);
            case 'formula':
                // Aquí se debe implementar un evaluador de fórmulas
                return 0;
            default:
                return 0;
        }
    }
}
