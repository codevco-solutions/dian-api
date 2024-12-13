<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\ProductTaxService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductTaxController extends Controller
{
    protected $productTaxService;

    public function __construct(ProductTaxService $productTaxService)
    {
        $this->productTaxService = $productTaxService;
    }

    /**
     * Get all taxes for a product
     */
    public function getProductTaxes($productId)
    {
        $taxes = $this->productTaxService->getProductTaxes($productId);

        return response()->json([
            'message' => 'Product taxes retrieved successfully',
            'data' => $taxes
        ]);
    }

    /**
     * Assign a tax to a product
     */
    public function assignTax(Request $request, $productId)
    {
        $request->validate([
            'tax_id' => 'required|exists:taxes,id',
            'rate' => 'nullable|numeric|min:0|max:100',
            'is_exempt' => 'nullable|boolean',
            'exemption_reason' => 'required_if:is_exempt,true|nullable|string',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'notes' => 'nullable|string'
        ]);

        try {
            $tax = $this->productTaxService->assignTax($productId, $request->all());

            return response()->json([
                'message' => 'Tax assigned successfully',
                'data' => $tax
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a product's tax
     */
    public function updateTax(Request $request, $productId, $taxId)
    {
        $request->validate([
            'rate' => 'nullable|numeric|min:0|max:100',
            'is_exempt' => 'nullable|boolean',
            'exemption_reason' => 'required_if:is_exempt,true|nullable|string',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'notes' => 'nullable|string'
        ]);

        try {
            $tax = $this->productTaxService->updateTax($productId, $taxId, $request->all());

            return response()->json([
                'message' => 'Tax updated successfully',
                'data' => $tax
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove a tax from a product
     */
    public function removeTax($productId, $taxId)
    {
        try {
            $this->productTaxService->removeTax($productId, $taxId);

            return response()->json([
                'message' => 'Tax removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get tax history for a product
     */
    public function getTaxHistory($productId)
    {
        $history = $this->productTaxService->getTaxHistory($productId);

        return response()->json([
            'message' => 'Tax history retrieved successfully',
            'data' => $history
        ]);
    }

    /**
     * Calculate taxes for a product
     */
    public function calculateTaxes(Request $request, $productId)
    {
        $request->validate([
            'base_amount' => 'required|numeric|min:0',
            'date' => 'nullable|date'
        ]);

        $calculation = $this->productTaxService->calculateTaxes(
            $productId,
            $request->base_amount,
            $request->date
        );

        return response()->json([
            'message' => 'Taxes calculated successfully',
            'data' => $calculation
        ]);
    }

    /**
     * Get tax summary for a product
     */
    public function getTaxSummary(Request $request, $productId)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after:date_from'
        ]);

        $summary = $this->productTaxService->getTaxSummary($productId, [
            'from' => $request->date_from,
            'to' => $request->date_to
        ]);

        return response()->json([
            'message' => 'Tax summary retrieved successfully',
            'data' => $summary
        ]);
    }
}
