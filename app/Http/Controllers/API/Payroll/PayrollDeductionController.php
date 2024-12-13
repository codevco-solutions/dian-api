<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\PayrollDeductionResource;
use App\Models\Payroll\PayrollDeduction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PayrollDeductionController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollDeduction::query();

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

        if ($request->has('is_mandatory')) {
            $query->where('is_mandatory', $request->boolean('is_mandatory'));
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
        $deductions = $query->paginate($perPage);

        return PayrollDeductionResource::collection($deductions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|string|max:20|unique:payroll_deductions,code',
            'name' => 'required|string|max:100',
            'type' => 'required|string',
            'calculation_type' => 'required|string',
            'value' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'formula' => 'nullable|string',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $deduction = PayrollDeduction::create($validated);

            DB::commit();

            return new PayrollDeductionResource($deduction);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(PayrollDeduction $deduction)
    {
        return new PayrollDeductionResource($deduction->load(['company', 'documentDeductions']));
    }

    public function update(Request $request, PayrollDeduction $deduction)
    {
        $validated = $request->validate([
            'code' => 'string|max:20|unique:payroll_deductions,code,' . $deduction->id,
            'name' => 'string|max:100',
            'type' => 'string',
            'calculation_type' => 'string',
            'value' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'formula' => 'nullable|string',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $deduction->update($validated);

            DB::commit();

            return new PayrollDeductionResource($deduction);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(PayrollDeduction $deduction)
    {
        // Verificar si tiene documentos asociados
        if ($deduction->hasDocuments()) {
            return response()->json([
                'message' => 'No se puede eliminar el concepto porque tiene documentos asociados'
            ], Response::HTTP_CONFLICT);
        }

        $deduction->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function activate(PayrollDeduction $deduction)
    {
        $deduction->activate();

        return new PayrollDeductionResource($deduction);
    }

    public function deactivate(PayrollDeduction $deduction)
    {
        $deduction->deactivate();

        return new PayrollDeductionResource($deduction);
    }

    public function calculate(PayrollDeduction $deduction, Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_id' => 'required|exists:payroll_periods,id',
            'base_value' => 'required|numeric|min:0'
        ]);

        try {
            $amount = $deduction->calculate(
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
