<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\PayrollAdjustmentResource;
use App\Models\Payroll\PayrollAdjustment;
use App\Services\DianService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PayrollAdjustmentController extends Controller
{
    protected $dianService;

    public function __construct(DianService $dianService)
    {
        $this->dianService = $dianService;
    }

    public function index(Request $request)
    {
        $query = PayrollAdjustment::query();

        // Filtros
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('payroll_document_id')) {
            $query->where('payroll_document_id', $request->payroll_document_id);
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

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $adjustments = $query->paginate($perPage);

        return PayrollAdjustmentResource::collection($adjustments);
    }

    public function show(PayrollAdjustment $adjustment)
    {
        return new PayrollAdjustmentResource($adjustment->load([
            'company', 'branch', 'employee', 'document', 'items',
            'logs', 'dianLogs', 'errorLogs'
        ]));
    }

    public function approve(PayrollAdjustment $adjustment)
    {
        // Verificar si el ajuste se puede aprobar
        if (!$adjustment->isApprovable()) {
            return response()->json([
                'message' => 'El ajuste no se puede aprobar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $adjustment->approve();

            // Enviar a la DIAN
            $this->dianService->sendPayrollAdjustment($adjustment);

            DB::commit();

            return new PayrollAdjustmentResource($adjustment);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancel(PayrollAdjustment $adjustment, Request $request)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string'
        ]);

        // Verificar si el ajuste se puede cancelar
        if (!$adjustment->isCancellable()) {
            return response()->json([
                'message' => 'El ajuste no se puede cancelar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $adjustment->cancel($validated['cancellation_reason']);

            // Notificar a la DIAN si es necesario
            if ($adjustment->isValidatedByDian()) {
                $this->dianService->cancelPayrollAdjustment($adjustment);
            }

            DB::commit();

            return new PayrollAdjustmentResource($adjustment);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function downloadPdf(PayrollAdjustment $adjustment)
    {
        $pdf = $adjustment->generatePdf();

        return response()->download(
            $pdf->getPath(),
            "ajuste_nomina_{$adjustment->number}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadXml(PayrollAdjustment $adjustment)
    {
        $xml = $adjustment->generateXml();

        return response()->download(
            $xml->getPath(),
            "ajuste_nomina_{$adjustment->number}.xml",
            ['Content-Type' => 'application/xml']
        );
    }
}
