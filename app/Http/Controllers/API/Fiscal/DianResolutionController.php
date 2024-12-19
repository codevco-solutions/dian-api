<?php

namespace App\Http\Controllers\Api\Fiscal;

use App\Http\Controllers\Controller;
use App\Models\Fiscal\DianResolution;
use App\Services\Fiscal\DianResolutionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DianResolutionController extends Controller
{
    protected $service;

    public function __construct(DianResolutionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['company_id', 'branch_id', 'type', 'is_active', 'per_page']);
        $resolutions = $this->service->getAll($filters);
        return response()->json($resolutions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'type' => ['required', Rule::in(['factura_venta', 'nota_credito', 'nota_debito'])],
            'resolution_number' => 'required|string',
            'prefix' => 'nullable|string|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|gt:start_number',
            'technical_key' => 'nullable|string'
        ]);

        $resolution = $this->service->create($validated);
        return response()->json($resolution, 201);
    }

    public function show(DianResolution $resolution)
    {
        return response()->json($resolution);
    }

    public function update(Request $request, DianResolution $resolution)
    {
        $validated = $request->validate([
            'is_active' => 'boolean',
            'technical_key' => 'nullable|string',
            'current_number' => 'integer|between:' . $resolution->start_number . ',' . $resolution->end_number
        ]);

        $resolution = $this->service->update($resolution, $validated);
        return response()->json($resolution);
    }

    public function getNextNumber(DianResolution $resolution)
    {
        $result = $this->service->getNextNumber($resolution);
        return response()->json($result);
    }
}
