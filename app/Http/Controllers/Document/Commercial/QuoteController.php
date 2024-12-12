<?php

namespace App\Http\Controllers\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\Commercial\QuoteResource;
use App\Models\Document\Commercial\Quote;
use App\Services\Document\Commercial\QuoteService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    protected $service;

    public function __construct(QuoteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $quotes = $this->service->all($request);
            return QuoteResource::collection($quotes);
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
                'date' => 'required|date',
                'expiration_date' => 'required|date|after:date',
                'currency_code' => 'required|string|size:3',
                'exchange_rate' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100'
            ]);

            $quote = $this->service->create($validated);
            return new QuoteResource($quote->load(['items']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Quote $quote)
    {
        try {
            return new QuoteResource($quote->load([
                'company', 'branch', 'customer', 'items', 'logs'
            ]));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Quote $quote)
    {
        try {
            $validated = $request->validate([
                'date' => 'date',
                'expiration_date' => 'date|after:date',
                'currency_code' => 'string|size:3',
                'exchange_rate' => 'numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'array|min:1',
                'items.*.id' => 'nullable|exists:quote_items,id',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100'
            ]);

            $quote = $this->service->update($quote->id, $validated);
            return new QuoteResource($quote->load(['items']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Quote $quote)
    {
        try {
            $this->service->delete($quote->id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function approve(Quote $quote)
    {
        try {
            $quote = $this->service->approve($quote->id);
            return new QuoteResource($quote->load(['items']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function reject(Quote $quote, Request $request)
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string'
            ]);

            $quote = $this->service->reject($quote->id, $validated['rejection_reason']);
            return new QuoteResource($quote->load(['items']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancel(Quote $quote, Request $request)
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'required|string'
            ]);

            $quote = $this->service->cancel($quote->id, $validated['cancellation_reason']);
            return new QuoteResource($quote->load(['items']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createOrder(Quote $quote)
    {
        try {
            $order = $this->service->createOrder($quote->id);
            return new OrderResource($order->load(['items']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
