<?php

namespace App\Http\Controllers\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\Commercial\InvoiceResource;
use App\Http\Resources\Document\Commercial\CreditNoteResource;
use App\Http\Resources\Document\Commercial\DebitNoteResource;
use App\Services\Document\Commercial\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $service;

    public function __construct(InvoiceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $invoices = $this->service->all($request->all());
            return InvoiceResource::collection($invoices);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'branch_id' => 'required|exists:branches,id',
                'customer_id' => 'required|exists:customers,id',
                'order_id' => 'nullable|exists:orders,id',
                'date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:date',
                'currency' => 'required|string|size:3',
                'exchange_rate' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'payment_term' => 'required|string',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.order_item_id' => 'nullable|exists:order_items,id',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.tax_rate' => 'required|numeric|min:0',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100'
            ]);

            $invoice = $this->service->create($validated);
            return new InvoiceResource($invoice);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $invoice = $this->service->find($id);
            return new InvoiceResource($invoice);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'date' => 'date',
                'due_date' => 'date|after_or_equal:date',
                'currency' => 'string|size:3',
                'exchange_rate' => 'numeric|min:0',
                'payment_method' => 'string',
                'payment_term' => 'string',
                'notes' => 'nullable|string',
                'items' => 'array|min:1',
                'items.*.id' => 'nullable|exists:invoice_items,id',
                'items.*.order_item_id' => 'nullable|exists:order_items,id',
                'items.*.product_id' => 'exists:products,id',
                'items.*.quantity' => 'numeric|min:0',
                'items.*.price' => 'numeric|min:0',
                'items.*.tax_rate' => 'numeric|min:0',
                'items.*.discount_rate' => 'numeric|min:0|max:100'
            ]);

            $invoice = $this->service->update($id, $validated);
            return new InvoiceResource($invoice);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function approve($id)
    {
        try {
            $invoice = $this->service->approve($id);
            return new InvoiceResource($invoice);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string'
            ]);

            $invoice = $this->service->cancel($id, $validated['reason']);
            return new InvoiceResource($invoice);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createCreditNote(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.price' => 'required|numeric|min:0'
            ]);

            $creditNote = $this->service->createCreditNote($id, $validated);
            return new CreditNoteResource($creditNote);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createDebitNote(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.price' => 'required|numeric|min:0'
            ]);

            $debitNote = $this->service->createDebitNote($id, $validated);
            return new DebitNoteResource($debitNote);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function registerPayment(Invoice $invoice, Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Verificar si se puede registrar el pago
        if (!$invoice->canRegisterPayment($validated['amount'])) {
            return response()->json([
                'message' => 'No se puede registrar el pago por el monto especificado'
            ], Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $payment = $invoice->registerPayment(
                $validated['amount'],
                $validated['payment_method'],
                $validated['reference'] ?? null,
                $validated['notes'] ?? null
            );

            DB::commit();

            return new InvoiceResource($invoice->load(['payments']));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function downloadPdf(Invoice $invoice)
    {
        $pdf = $invoice->generatePdf();

        return response()->download(
            $pdf->getPath(),
            "factura_{$invoice->number}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadXml(Invoice $invoice)
    {
        $xml = $invoice->generateXml();

        return response()->download(
            $xml->getPath(),
            "factura_{$invoice->number}.xml",
            ['Content-Type' => 'application/xml']
        );
    }
}
