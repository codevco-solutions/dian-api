<?php

namespace App\Http\Controllers\API\Fiscal;

use App\Http\Controllers\API\BaseAPIController;
use App\Services\Fiscal\RegionalTaxRuleService;
use Illuminate\Http\Request;

class RegionalTaxRuleController extends BaseAPIController
{
    protected $service;

    public function __construct(RegionalTaxRuleService $service)
    {
        $this->service = $service;
    }

    public function getByLocation(Request $request)
    {
        try {
            $rules = [
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'nullable|exists:cities,id'
            ];

            $this->validate($request, $rules);

            $rules = $this->service->getByLocation(
                $request->country_id,
                $request->state_id,
                $request->city_id
            );

            return $this->sendResponse($rules, 'Reglas de impuestos regionales obtenidas con éxito');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener las reglas de impuestos regionales', $e);
        }
    }

    public function getByTaxRule($taxRuleId)
    {
        try {
            $rules = $this->service->getByTaxRule($taxRuleId);
            return $this->sendResponse($rules, 'Reglas regionales por impuesto obtenidas con éxito');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener las reglas regionales por impuesto', $e);
        }
    }
}
