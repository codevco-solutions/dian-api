<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\PayrollPeriodResource;
use App\Models\Payroll\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollPeriod::query();

        // Filtros
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('month')) {
            $query->where('month', $request->month);
        }

        if ($request->has('period')) {
            $query->where('period', $request->period);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $periods = $query->paginate($perPage);

        return PayrollPeriodResource::collection($periods);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|string',
            'year' => 'required|integer|min:2000',
            'month' => 'required|integer|min:1|max:12',
            'period' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_date' => 'required|date|after_or_equal:end_date',
            'metadata' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            // Verificar si ya existe un período con los mismos datos
            $exists = PayrollPeriod::where('company_id', $validated['company_id'])
                ->where('type', $validated['type'])
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->where('period', $validated['period'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Ya existe un período con los mismos datos'
                ], Response::HTTP_CONFLICT);
            }

            $period = PayrollPeriod::create($validated);

            DB::commit();

            return new PayrollPeriodResource($period);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(PayrollPeriod $period)
    {
        return new PayrollPeriodResource($period->load(['company', 'payrollDocuments']));
    }

    public function update(Request $request, PayrollPeriod $period)
    {
        // Verificar si el período es editable
        if (!$period->isEditable()) {
            return response()->json([
                'message' => 'El período no se puede editar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        $validated = $request->validate([
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'payment_date' => 'date|after_or_equal:end_date',
            'metadata' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $period->update($validated);

            DB::commit();

            return new PayrollPeriodResource($period);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(PayrollPeriod $period)
    {
        // Verificar si el período se puede eliminar
        if (!$period->isDeletable()) {
            return response()->json([
                'message' => 'El período no se puede eliminar porque tiene documentos asociados'
            ], Response::HTTP_CONFLICT);
        }

        $period->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function open(PayrollPeriod $period)
    {
        // Verificar si el período se puede abrir
        if (!$period->canOpen()) {
            return response()->json([
                'message' => 'El período no se puede abrir en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        $period->open();

        return new PayrollPeriodResource($period);
    }

    public function close(PayrollPeriod $period)
    {
        // Verificar si el período se puede cerrar
        if (!$period->canClose()) {
            return response()->json([
                'message' => 'El período no se puede cerrar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $period->close();

            DB::commit();

            return new PayrollPeriodResource($period);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reopen(PayrollPeriod $period)
    {
        // Verificar si el período se puede reabrir
        if (!$period->canReopen()) {
            return response()->json([
                'message' => 'El período no se puede reabrir en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $period->reopen();

            DB::commit();

            return new PayrollPeriodResource($period);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function generateDocuments(PayrollPeriod $period)
    {
        // Verificar si se pueden generar documentos
        if (!$period->canGenerateDocuments()) {
            return response()->json([
                'message' => 'No se pueden generar documentos en el estado actual del período'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $documents = $period->generateDocuments();

            DB::commit();

            return PayrollDocumentResource::collection($documents);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
