<?php

namespace App\Models\Fiscal;

use App\Models\BaseModel;
use App\Models\Company\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalCalendar extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'start_date',
        'end_date',
        'due_dates',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'due_dates' => 'json',
        'is_active' => 'boolean'
    ];

    // Relaciones
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Métodos
    public function getNextDueDate()
    {
        $dueDates = collect($this->due_dates);
        return $dueDates->first(function ($date) {
            return now()->lt($date);
        });
    }

    public function getDaysUntilNextDueDate()
    {
        $nextDueDate = $this->getNextDueDate();
        return $nextDueDate ? now()->diffInDays($nextDueDate, false) : null;
    }

    public function isInCurrentPeriod()
    {
        return now()->between($this->start_date, $this->end_date);
    }

    public function getPeriodDescription()
    {
        return sprintf(
            '%s (%s - %s)',
            $this->name,
            $this->start_date->format('Y-m-d'),
            $this->end_date->format('Y-m-d')
        );
    }
}
