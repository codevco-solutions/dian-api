<?php

namespace App\Models\Payroll;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Document\DianLog;
use App\Models\Document\DocLog;
use App\Models\Document\ErrorLog;
use Illuminate\Database\Eloquent\Model;

class PayrollDocument extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'employee_id',
        'payroll_period_id',
        'number',
        'prefix',
        'date',
        'worked_days',
        'total_earnings',
        'total_deductions',
        'net_pay',
        'status',
        'uuid',
        'qr_data',
        'metadata'
    ];

    protected $casts = [
        'date' => 'date',
        'worked_days' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'qr_data' => 'json',
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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function earnings()
    {
        return $this->hasMany(PayrollDocumentEarning::class);
    }

    public function deductions()
    {
        return $this->hasMany(PayrollDocumentDeduction::class);
    }

    public function adjustments()
    {
        return $this->hasMany(PayrollAdjustment::class);
    }

    public function logs()
    {
        return $this->morphMany(DocLog::class, 'documentable');
    }

    public function dianLogs()
    {
        return $this->morphMany(DianLog::class, 'documentable');
    }

    public function errorLogs()
    {
        return $this->morphMany(ErrorLog::class, 'documentable');
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function isEditable()
    {
        return in_array($this->status, ['draft']);
    }

    public function calculateTotals()
    {
        $this->total_earnings = $this->earnings->sum('amount');
        $this->total_deductions = $this->deductions->sum('amount');
        $this->net_pay = $this->total_earnings - $this->total_deductions;
        $this->save();
    }
}
