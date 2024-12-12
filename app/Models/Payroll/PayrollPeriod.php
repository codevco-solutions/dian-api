<?php

namespace App\Models\Payroll;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'year',
        'month',
        'period',
        'start_date',
        'end_date',
        'payment_date',
        'status',
        'metadata'
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'period' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_date' => 'date',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function payrollDocuments()
    {
        return $this->hasMany(PayrollDocument::class);
    }

    public function getNameAttribute()
    {
        $name = "{$this->year}-{$this->month}";
        if ($this->type === 'biweekly') {
            $name .= "-{$this->period}";
        }
        return $name;
    }

    public function getDaysInPeriod()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeOfMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    public function scopeOfPeriod($query, $period)
    {
        return $query->where('period', $period);
    }
}
