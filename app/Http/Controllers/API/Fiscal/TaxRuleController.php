<?php

namespace App\Http\Controllers\Api\Fiscal;

use App\Http\Controllers\Controller;
use App\Models\Fiscal\TaxRule;
use App\Services\Fiscal\TaxRuleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaxRuleController extends Controller
{
    protected $service;

    public function __construct(TaxRuleService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['company_id', 'type', 'is_active', 'per_page']);
        $rules = $this->service->getAll($filters);
        return response()->json($rules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['iva', 'retencion_iva', 'retencion_fuente', 'retencion_ica'])],
            'rate' => 'required|numeric|between:0,100',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount',
            'conditions' => 'nullable|json',
            'is_active' => 'boolean'
        ]);

        $rule = $this->service->create($validated);
        return response()->json($rule, 201);
    }

    public function show(TaxRule $rule)
    {
        return response()->json($rule->load('regionalRules'));
    }

    public function update(Request $request, TaxRule $rule)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'rate' => 'numeric|between:0,100',
            'min_amount' => 'numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount',
            'conditions' => 'nullable|json',
            'is_active' => 'boolean'
        ]);

        $rule = $this->service->update($rule, $validated);
        return response()->json($rule);
    }

    public function calculateTax(Request $request, TaxRule $rule)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'location' => 'nullable|array',
            'location.country_id' => 'required_with:location|exists:countries,id',
            'location.state_id' => 'required_with:location|exists:states,id',
            'location.city_id' => 'nullable|exists:cities,id'
        ]);

        $result = $this->service->calculateTax($rule, $request->amount, $request->location);
        return response()->json($result);
    }
}
