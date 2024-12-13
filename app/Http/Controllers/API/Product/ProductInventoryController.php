<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\ProductInventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductInventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(ProductInventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get product inventory for a branch
     */
    public function getInventory(Request $request, $productId, $branchId)
    {
        $inventory = $this->inventoryService->getInventory($productId, $branchId);

        return response()->json([
            'message' => 'Inventory retrieved successfully',
            'data' => $inventory
        ]);
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Request $request, $productId, $branchId)
    {
        $request->validate([
            'quantity' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $movement = $this->inventoryService->updateStock(
            $productId,
            $branchId,
            $request->quantity,
            [
                'notes' => $request->notes
            ]
        );

        return response()->json([
            'message' => 'Stock updated successfully',
            'data' => $movement
        ]);
    }

    /**
     * Add stock
     */
    public function addStock(Request $request, $productId, $branchId)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        $movement = $this->inventoryService->addStock(
            $productId,
            $branchId,
            $request->quantity,
            [
                'reference_type' => $request->reference_type,
                'reference_id' => $request->reference_id,
                'notes' => $request->notes
            ]
        );

        return response()->json([
            'message' => 'Stock added successfully',
            'data' => $movement
        ]);
    }

    /**
     * Remove stock
     */
    public function removeStock(Request $request, $productId, $branchId)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        try {
            $movement = $this->inventoryService->removeStock(
                $productId,
                $branchId,
                $request->quantity,
                [
                    'reference_type' => $request->reference_type,
                    'reference_id' => $request->reference_id,
                    'notes' => $request->notes
                ]
            );

            return response()->json([
                'message' => 'Stock removed successfully',
                'data' => $movement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Transfer stock between branches
     */
    public function transferStock(Request $request, $productId)
    {
        $request->validate([
            'from_branch_id' => 'required|integer|exists:branches,id',
            'to_branch_id' => 'required|integer|exists:branches,id|different:from_branch_id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ]);

        try {
            $movement = $this->inventoryService->transferStock(
                $productId,
                $request->from_branch_id,
                $request->to_branch_id,
                $request->quantity,
                $request->notes
            );

            return response()->json([
                'message' => 'Stock transferred successfully',
                'data' => $movement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get inventory movements
     */
    public function getMovements(Request $request, $productId, $branchId)
    {
        $filters = $request->only([
            'type',
            'date_from',
            'date_to',
            'per_page'
        ]);

        $movements = $this->inventoryService->getMovements($productId, $branchId, $filters);

        return response()->json([
            'message' => 'Movements retrieved successfully',
            'data' => $movements
        ]);
    }

    /**
     * Get stock alerts for a branch
     */
    public function getStockAlerts(Request $request, $branchId)
    {
        $alerts = $this->inventoryService->getStockAlerts($branchId);

        return response()->json([
            'message' => 'Stock alerts retrieved successfully',
            'data' => $alerts
        ]);
    }
}
