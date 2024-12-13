<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\PriceHistoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PriceHistoryController extends Controller
{
    protected $priceHistoryService;

    public function __construct(PriceHistoryService $priceHistoryService)
    {
        $this->priceHistoryService = $priceHistoryService;
    }

    /**
     * Get price history for a product
     */
    public function getProductHistory(Request $request, $productId)
    {
        $filters = $request->only([
            'price_list_id',
            'date_from',
            'date_to',
            'per_page'
        ]);

        $history = $this->priceHistoryService->getProductPriceHistory($productId, $filters);

        return response()->json([
            'message' => 'Price history retrieved successfully',
            'data' => $history
        ]);
    }

    /**
     * Get price history for a price list
     */
    public function getPriceListHistory(Request $request, $priceListId)
    {
        $filters = $request->only([
            'product_id',
            'date_from',
            'date_to',
            'per_page'
        ]);

        $history = $this->priceHistoryService->getPriceListHistory($priceListId, $filters);

        return response()->json([
            'message' => 'Price list history retrieved successfully',
            'data' => $history
        ]);
    }

    /**
     * Record a price change
     */
    public function recordPriceChange(Request $request, $productId, $priceListId)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
            'reason' => 'nullable|string'
        ]);

        $history = $this->priceHistoryService->recordPriceChange(
            $productId,
            $priceListId,
            $request->price,
            $request->only(['effective_date', 'reason'])
        );

        return response()->json([
            'message' => 'Price change recorded successfully',
            'data' => $history
        ]);
    }

    /**
     * Get price evolution
     */
    public function getPriceEvolution(Request $request, $productId, $priceListId)
    {
        $request->validate([
            'period' => 'nullable|string|in:1week,1month,3months,6months,1year'
        ]);

        $evolution = $this->priceHistoryService->getPriceEvolution(
            $productId,
            $priceListId,
            $request->period ?? '1month'
        );

        return response()->json([
            'message' => 'Price evolution retrieved successfully',
            'data' => $evolution
        ]);
    }

    /**
     * Get price trends analysis
     */
    public function getPriceTrends(Request $request, $productId, $priceListId)
    {
        $trends = $this->priceHistoryService->analyzePriceTrends($productId, $priceListId);

        return response()->json([
            'message' => 'Price trends analyzed successfully',
            'data' => $trends
        ]);
    }
}
