<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\PayrollEarningResource;
use App\Models\Payroll\PayrollEarning;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PayrollEarningController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollEarning::query();

        // Filtros
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $earnings = $query->paginate($perPage);

        return PayrollEarningResource::collection($earnings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|string|max:20|unique:payroll_earnings,code',
            'name' => 'required|string|max:100',
            'type' => 'required|string',
            'calculation_type' => 'required|string',
            'value' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'formula' => 'nullable|string',
            'affects_social_security' => 'boolean',
            'affects_parafiscal' => 'boolean',
            'affects_retention' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $earning = PayrollEarning::create($validated);

            DB::commit();

            return new PayrollEarningResource($earning);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(PayrollEarning $earning)
    {
        return new PayrollEarningResource($earning->load(['company', 'documentEarnings']));
    }

    public function update(Request $request, PayrollEarning $earning)
    {
        $validated = $request->validate([
            'code' => 'string|max:20|unique:payroll_earnings,code,' . $earning->id,
            'name' => 'string|max:100',
            'type' => 'string',
            'calculation_type' => 'string',
            'value' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'formula' => 'nullable|string',
            'affects_social_security' => 'boolean',
            'affects_parafiscal' => 'boolean',
            'affects_retention' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $earning->update($validated);

            DB::commit();

            return new PayrollEarningResource($earning);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(PayrollEarning $earning)
    {
        // Verificar si tiene documentos asociados
        if ($earning->hasDocuments()) {
            return response()->json([
                'message' => 'No se puede eliminar el concepto porque tiene documentos asociados'
            ], Response::HTTP_CONFLICT);
        }

        $earning->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function activate(PayrollEarning $earning)
    {
        $earning->activate();

        return new PayrollEarningResource($earning);
    }

    public function deactivate(PayrollEarning $earning)
    {
        $earning->deactivate();

        return new PayrollEarningResource($earning);
    }

    public function calculate(PayrollEarning $earning, Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_id' => 'required|exists:payroll_periods,id',
            'base_value' => 'required|numeric|min:0'
        ]);

        try {
            $amount = $earning->calculate(
                $validated['employee_id'],
                $validated['period_id'],
                $validated['base_value']
            );

            return response()->json([
                'amount' => $amount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al calcular el concepto: ' . $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
