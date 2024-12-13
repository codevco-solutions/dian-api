<?php

namespace App\Http\Controllers\API\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\Commercial\DebitNoteResource;
use App\Models\Document\Commercial\DebitNote;
use App\Services\Document\Commercial\DebitNoteService;
use App\Services\DianService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DebitNoteController extends Controller
{
    protected $debitNoteService;
    protected $dianService;

    public function __construct(DebitNoteService $debitNoteService, DianService $dianService)
    {
        $this->debitNoteService = $debitNoteService;
        $this->dianService = $dianService;
    }

    public function index(Request $request)
    {
        $query = DebitNote::query();

        // Filtros
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
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
        $debitNotes = $query->paginate($perPage);

        return DebitNoteResource::collection($debitNotes);
    }

    public function show(DebitNote $debitNote)
    {
        return new DebitNoteResource($debitNote->load([
            'company', 'branch', 'customer', 'invoice', 'items',
            'logs', 'dianLogs', 'errorLogs'
        ]));
    }

    public function approve(DebitNote $debitNote)
    {
        // Verificar si la nota débito se puede aprobar
        if (!$debitNote->isApprovable()) {
            return response()->json([
                'message' => 'La nota débito no se puede aprobar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $debitNote->approve();

            // Enviar a la DIAN
            $this->dianService->sendDebitNote($debitNote);

            DB::commit();

            return new DebitNoteResource($debitNote->load(['items']));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancel(DebitNote $debitNote, Request $request)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string'
        ]);

        // Verificar si la nota débito se puede cancelar
        if (!$debitNote->isCancellable()) {
            return response()->json([
                'message' => 'La nota débito no se puede cancelar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $debitNote->cancel($validated['cancellation_reason']);

            // Notificar a la DIAN si es necesario
            if ($debitNote->isValidatedByDian()) {
                $this->dianService->cancelDebitNote($debitNote);
            }

            DB::commit();

            return new DebitNoteResource($debitNote->load(['items']));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function downloadPdf(DebitNote $debitNote)
    {
        $pdf = $debitNote->generatePdf();

        return response()->download(
            $pdf->getPath(),
            "nota_debito_{$debitNote->number}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadXml(DebitNote $debitNote)
    {
        $xml = $debitNote->generateXml();

        return response()->download(
            $xml->getPath(),
            "nota_debito_{$debitNote->number}.xml",
            ['Content-Type' => 'application/xml']
        );
    }
}
