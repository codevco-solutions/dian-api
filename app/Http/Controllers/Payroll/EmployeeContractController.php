<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\EmployeeContractResource;
use App\Models\Payroll\EmployeeContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class EmployeeContractController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeContract::query();

        // Filtros
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $contracts = $query->paginate($perPage);

        return EmployeeContractResource::collection($contracts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'base_salary' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_frequency' => 'required|string',
            'working_hours_week' => 'required|integer|min:0|max:168',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            // Si el contrato es activo, desactivar otros contratos activos
            if ($validated['is_active'] ?? false) {
                EmployeeContract::where('employee_id', $validated['employee_id'])
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $contract = EmployeeContract::create($validated);

            DB::commit();

            return new EmployeeContractResource($contract);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(EmployeeContract $contract)
    {
        return new EmployeeContractResource($contract->load(['employee']));
    }

    public function update(Request $request, EmployeeContract $contract)
    {
        $validated = $request->validate([
            'type' => 'string',
            'position' => 'string|max:100',
            'department' => 'string|max:100',
            'start_date' => 'date',
            'end_date' => 'nullable|date|after:start_date',
            'base_salary' => 'numeric|min:0',
            'payment_method' => 'string',
            'payment_frequency' => 'string',
            'working_hours_week' => 'integer|min:0|max:168',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            // Si se está activando el contrato, desactivar otros contratos activos
            if (isset($validated['is_active']) && $validated['is_active']) {
                EmployeeContract::where('employee_id', $contract->employee_id)
                    ->where('id', '!=', $contract->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $contract->update($validated);

            DB::commit();

            return new EmployeeContractResource($contract);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(EmployeeContract $contract)
    {
        // Verificar si tiene documentos asociados
        if ($contract->hasDocuments()) {
            return response()->json([
                'message' => 'No se puede eliminar el contrato porque tiene documentos asociados'
            ], Response::HTTP_CONFLICT);
        }

        $contract->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function terminate(EmployeeContract $contract, Request $request)
    {
        $validated = $request->validate([
            'end_date' => 'required|date|after:start_date',
            'termination_reason' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $contract->terminate(
                $validated['end_date'],
                $validated['termination_reason']
            );

            DB::commit();

            return new EmployeeContractResource($contract);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function renew(EmployeeContract $contract, Request $request)
    {
        $validated = $request->validate([
            'end_date' => 'required|date|after:start_date',
            'base_salary' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $contract->renew(
                $validated['end_date'],
                $validated['base_salary'] ?? null
            );

            DB::commit();

            return new EmployeeContractResource($contract);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
