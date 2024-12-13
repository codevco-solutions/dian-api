<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\EmployeeResource;
use App\Models\Payroll\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        // Filtros
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('identification_type_id')) {
            $query->where('identification_type_id', $request->identification_type_id);
        }

        if ($request->has('identification_number')) {
            $query->where('identification_number', $request->identification_number);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('second_last_name', 'like', "%{$search}%")
                    ->orWhere('identification_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $employees = $query->paginate($perPage);

        return EmployeeResource::collection($employees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'identification_type_id' => 'required|exists:identification_types,id',
            'identification_number' => 'required|string|unique:employees,identification_number',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'second_last_name' => 'nullable|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|string|in:M,F',
            'marital_status' => 'required|string',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'nullable|string|max:10',
            'bank_name' => 'required|string|max:100',
            'bank_account_type' => 'required|string|in:savings,checking',
            'bank_account_number' => 'required|string|max:50',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::create($validated);

            DB::commit();

            return new EmployeeResource($employee);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Employee $employee)
    {
        return new EmployeeResource($employee->load([
            'company', 'branch', 'identificationType', 'country', 'state', 'city',
            'contracts', 'activeContract', 'payrollDocuments', 'payrollAdjustments'
        ]));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'branch_id' => 'exists:branches,id',
            'identification_type_id' => 'exists:identification_types,id',
            'identification_number' => 'string|unique:employees,identification_number,' . $employee->id,
            'first_name' => 'string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'string|max:100',
            'second_last_name' => 'nullable|string|max:100',
            'birth_date' => 'date',
            'gender' => 'string|in:M,F',
            'marital_status' => 'string',
            'email' => 'email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'string|max:255',
            'country_id' => 'exists:countries,id',
            'state_id' => 'exists:states,id',
            'city_id' => 'exists:cities,id',
            'postal_code' => 'nullable|string|max:10',
            'bank_name' => 'string|max:100',
            'bank_account_type' => 'string|in:savings,checking',
            'bank_account_number' => 'string|max:50',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $employee->update($validated);

            DB::commit();

            return new EmployeeResource($employee);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Employee $employee)
    {
        // Verificar si tiene documentos asociados
        if ($employee->hasDocuments()) {
            return response()->json([
                'message' => 'No se puede eliminar el empleado porque tiene documentos asociados'
            ], Response::HTTP_CONFLICT);
        }

        $employee->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function activate(Employee $employee)
    {
        $employee->activate();

        return new EmployeeResource($employee);
    }

    public function deactivate(Employee $employee)
    {
        $employee->deactivate();

        return new EmployeeResource($employee);
    }

    public function getActiveContract(Employee $employee)
    {
        $contract = $employee->activeContract;

        if (!$contract) {
            return response()->json([
                'message' => 'El empleado no tiene un contrato activo'
            ], Response::HTTP_NOT_FOUND);
        }

        return new EmployeeContractResource($contract);
    }

    public function getPayrollDocuments(Employee $employee, Request $request)
    {
        $query = $employee->payrollDocuments();

        if ($request->has('period_id')) {
            $query->where('payroll_period_id', $request->period_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $documents = $query->orderBy('date', 'desc')->get();

        return PayrollDocumentResource::collection($documents);
    }

    public function getPayrollAdjustments(Employee $employee, Request $request)
    {
        $query = $employee->payrollAdjustments();

        if ($request->has('document_id')) {
            $query->where('payroll_document_id', $request->document_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $adjustments = $query->orderBy('date', 'desc')->get();

        return PayrollAdjustmentResource::collection($adjustments);
    }
}
