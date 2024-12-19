<?php

namespace App\Http\Controllers\API\Fiscal;

use App\Http\Controllers\API\BaseAPIController;
use App\Services\Fiscal\FiscalCalendarService;
use Illuminate\Http\Request;

class FiscalCalendarController extends BaseAPIController
{
    protected $service;

    public function __construct(FiscalCalendarService $service)
    {
        $this->service = $service;
    }

    public function getCurrentPeriods()
    {
        try {
            $periods = $this->service->getCurrentPeriods();
            return $this->sendResponse($periods, 'Períodos fiscales actuales obtenidos con éxito');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener los períodos fiscales actuales', $e);
        }
    }

    public function getUpcomingDueDates(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            $dueDates = $this->service->getUpcomingDueDates($limit);
            return $this->sendResponse($dueDates, 'Próximas fechas de vencimiento obtenidas con éxito');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener las próximas fechas de vencimiento', $e);
        }
    }
}
