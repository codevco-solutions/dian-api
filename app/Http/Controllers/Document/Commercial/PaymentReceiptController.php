<?php

namespace App\Http\Controllers\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\Commercial\PaymentReceiptResource;
use App\Models\Document\Commercial\PaymentReceipt;
use App\Services\Document\Commercial\PaymentReceiptService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentReceiptController extends Controller
{
    protected $paymentReceiptService;

    public function __construct(PaymentReceiptService $paymentReceiptService)
    {
        $this->paymentReceiptService = $paymentReceiptService;
    }

    public function index(Request $request)
    {
        $query = PaymentReceipt::query();

        // Filtros
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('partner_type')) {
            $query->where('partner_type', $request->partner_type);
        }

        if ($request->has('partner_id')) {
            $query->where('partner_id', $request->partner_id);
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
        $receipts = $query->paginate($perPage);

        return PaymentReceiptResource::collection($receipts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'partner_type' => 'required|string',
            'partner_id' => 'required|integer',
            'date' => 'required|date',
            'currency_code' => 'required|string|size:3',
            'exchange_rate' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.document_type' => 'required|string',
            'details.*.document_id' => 'required|integer',
            'details.*.amount' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Crear recibo
            $receipt = $this->paymentReceiptService->store($validated);

            // Crear detalles
            foreach ($validated['details'] as $detailData) {
                $receipt->details()->create($detailData);
            }

            // Actualizar saldos de documentos
            foreach ($receipt->details as $detail) {
                $detail->document->updateBalance($detail->amount);
            }

            DB::commit();

            return new PaymentReceiptResource($receipt->load(['details']));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(PaymentReceipt $receipt)
    {
        return new PaymentReceiptResource($receipt->load([
            'company', 'branch', 'partner', 'details.document', 'logs'
        ]));
    }

    public function cancel(PaymentReceipt $receipt, Request $request)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string'
        ]);

        // Verificar si el recibo se puede cancelar
        if (!$receipt->isCancellable()) {
            return response()->json([
                'message' => 'El recibo no se puede cancelar en su estado actual'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            // Revertir saldos de documentos
            foreach ($receipt->details as $detail) {
                $detail->document->revertBalance($detail->amount);
            }

            $this->paymentReceiptService->cancel($receipt->id, $validated['cancellation_reason']);

            DB::commit();

            return new PaymentReceiptResource($receipt->load(['details']));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function downloadPdf(PaymentReceipt $receipt)
    {
        $pdf = $this->paymentReceiptService->generatePdf($receipt->id);

        return response()->download(
            $pdf->getPath(),
            "recibo_{$receipt->number}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }
}
