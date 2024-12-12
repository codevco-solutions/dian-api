<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\PayrollDocumentResource;
use App\Models\Payroll\PayrollDocument;
use App\Services\DianService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PayrollDocumentController extends Controller
{
    protected $dianService;

    public function __construct(DianService $dianService)
    {
        $this->dianService = $dianService;
    }

    public function index(Request $request)
    {
        $query = PayrollDocument::query();

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

        if ($request->has('payroll_period_id')) {
            $query->where('payroll_period_id', $request->payroll_period_id);
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
        $documents = $query->paginate($perPage);

        return PayrollDocumentResource::collection($documents);
    }

    public function show(PayrollDocument $document)
    {
        return new PayrollDocumentResource($document->load([
            'company', 'branch', 'employee', 'period',
            'earnings', 'deductions', 'adjustments',
            'logs', 'dianLogs', 'errorLogs'
        ]));
    }

    public function approve(PayrollDocument $document)
    {
        // Verificar si el documento se puede aprobar
        if (!$document->isApprovable()) {
            return response()->json([
                'message' => 'El documento no se puede aprobar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $document->approve();

            // Enviar a la DIAN
            $this->dianService->sendPayrollDocument($document);

            DB::commit();

            return new PayrollDocumentResource($document);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancel(PayrollDocument $document, Request $request)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string'
        ]);

        // Verificar si el documento se puede cancelar
        if (!$document->isCancellable()) {
            return response()->json([
                'message' => 'El documento no se puede cancelar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $document->cancel($validated['cancellation_reason']);

            // Notificar a la DIAN si es necesario
            if ($document->isValidatedByDian()) {
                $this->dianService->cancelPayrollDocument($document);
            }

            DB::commit();

            return new PayrollDocumentResource($document);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createAdjustment(PayrollDocument $document, Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.concept_type' => 'required|string',
            'items.*.concept_id' => 'required|integer',
            'items.*.adjustment_amount' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        // Verificar si se puede crear ajuste
        if (!$document->canCreateAdjustment()) {
            return response()->json([
                'message' => 'No se puede crear ajuste para este documento'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $adjustment = $document->createAdjustment($validated['items'], $validated['notes'] ?? null);

            // Enviar a la DIAN
            $this->dianService->sendPayrollAdjustment($adjustment);

            DB::commit();

            return new PayrollAdjustmentResource($adjustment->load(['items']));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function downloadPdf(PayrollDocument $document)
    {
        $pdf = $document->generatePdf();

        return response()->download(
            $pdf->getPath(),
            "nomina_{$document->number}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadXml(PayrollDocument $document)
    {
        $xml = $document->generateXml();

        return response()->download(
            $xml->getPath(),
            "nomina_{$document->number}.xml",
            ['Content-Type' => 'application/xml']
        );
    }
}
