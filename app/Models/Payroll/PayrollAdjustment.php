<?php

namespace App\Models\Payroll;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Document\DianLog;
use App\Models\Document\DocLog;
use App\Models\Document\ErrorLog;
use Illuminate\Database\Eloquent\Model;

class PayrollAdjustment extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'employee_id',
        'payroll_document_id',
        'number',
        'prefix',
        'date',
        'total_earnings',
        'total_deductions',
        'net_adjustment',
        'notes',
        'status',
        'uuid',
        'metadata'
    ];

    protected $casts = [
        'date' => 'date',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_adjustment' => 'decimal:2',
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

    public function document()
    {
        return $this->belongsTo(PayrollDocument::class, 'payroll_document_id');
    }

    public function items()
    {
        return $this->hasMany(PayrollAdjustmentItem::class);
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
        $this->total_earnings = $this->items()
            ->whereHasMorph('concept', [PayrollEarning::class])
            ->sum('adjustment_amount');

        $this->total_deductions = $this->items()
            ->whereHasMorph('concept', [PayrollDeduction::class])
            ->sum('adjustment_amount');

        $this->net_adjustment = $this->total_earnings - $this->total_deductions;
        $this->save();
    }
}
