<?php

namespace App\Http\Controllers\API\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\Commercial\OrderResource;
use App\Http\Resources\Document\Commercial\InvoiceResource;
use App\Services\Document\Commercial\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $orders = $this->service->all($request->all());
            return OrderResource::collection($orders);
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
                'partner_type' => 'required|string',
                'partner_id' => 'required|integer',
                'type' => 'required|string',
                'quote_id' => 'nullable|exists:quotes,id',
                'date' => 'required|date',
                'delivery_date' => 'required|date|after:date',
                'currency_code' => 'required|string|size:3',
                'exchange_rate' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.quote_item_id' => 'nullable|exists:quote_items,id',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100'
            ]);

            $order = $this->service->create($validated);
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Order $order)
    {
        try {
            return new OrderResource($order->load([
                'company', 'branch', 'partner', 'quote', 'items', 'logs'
            ]));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'date' => 'date',
                'delivery_date' => 'date|after:date',
                'currency_code' => 'string|size:3',
                'exchange_rate' => 'numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'array|min:1',
                'items.*.id' => 'nullable|exists:order_items,id',
                'items.*.quote_item_id' => 'nullable|exists:quote_items,id',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100'
            ]);

            $order = $this->service->update($order->id, $validated);
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Order $order)
    {
        try {
            $this->service->delete($order->id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function approve(Order $order)
    {
        try {
            $order = $this->service->approve($order->id);
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string'
            ]);

            $order = $this->service->reject($order->id, $validated['rejection_reason']);
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancel(Order $order, Request $request)
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'required|string'
            ]);

            $order = $this->service->cancel($order->id, $validated['cancellation_reason']);
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createInvoice(Order $order)
    {
        try {
            $invoice = $this->service->createInvoice($order->id);
            return new InvoiceResource($invoice);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
