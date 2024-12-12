<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'position',
        'department',
        'start_date',
        'end_date',
        'base_salary',
        'payment_method',
        'payment_frequency',
        'working_hours_week',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'base_salary' => 'decimal:2',
        'working_hours_week' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'json'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function isIndefinite()
    {
        return $this->type === 'indefinido';
    }

    public function isExpired()
    {
        return !$this->isIndefinite() && 
            $this->end_date !== null && 
            now()->greaterThan($this->end_date);
    }

    public function getDurationInDays()
    {
        $startDate = $this->start_date;
        $endDate = $this->end_date ?? now();
        return $startDate->diffInDays($endDate);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($contract) {
            // Si se está activando este contrato, desactivar otros contratos del empleado
            if ($contract->is_active) {
                static::where('employee_id', $contract->employee_id)
                    ->where('id', '!=', $contract->id)
                    ->update(['is_active' => false]);
            }
        });
    }
}
