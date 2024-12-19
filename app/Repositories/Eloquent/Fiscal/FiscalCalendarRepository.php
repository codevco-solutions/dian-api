<?php

namespace App\Repositories\Fiscal;

use App\Models\Fiscal\FiscalCalendar;
use App\Repositories\BaseRepository;
use App\Repositories\Fiscal\Interfaces\FiscalCalendarRepositoryInterface;

class FiscalCalendarRepository extends BaseRepository implements FiscalCalendarRepositoryInterface
{
    public function __construct(FiscalCalendar $model)
    {
        parent::__construct($model);
    }

    public function getCurrentPeriods()
    {
        return $this->model
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
    }

    public function getUpcomingDueDates($limit = 5)
    {
        $calendars = $this->model
            ->where('is_active', true)
            ->where('end_date', '>=', now())
            ->get();

        $dueDates = collect();
        
        foreach ($calendars as $calendar) {
            $nextDueDate = $calendar->getNextDueDate();
            if ($nextDueDate) {
                $dueDates->push([
                    'calendar' => $calendar,
                    'due_date' => $nextDueDate,
                    'days_until' => $calendar->getDaysUntilNextDueDate()
                ]);
            }
        }

        return $dueDates
            ->sortBy('due_date')
            ->take($limit)
            ->values();
    }
}
