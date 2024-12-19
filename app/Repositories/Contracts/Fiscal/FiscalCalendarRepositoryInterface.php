<?php

namespace App\Repositories\Fiscal\Interfaces;

use App\Repositories\BaseRepositoryInterface;

interface FiscalCalendarRepositoryInterface extends BaseRepositoryInterface
{
    public function getCurrentPeriods();
    public function getUpcomingDueDates($limit = 5);
}
