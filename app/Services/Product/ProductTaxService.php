<?php

namespace App\Services\Product;

use App\Repositories\Contracts\Product\ProductTaxRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductTaxService
{
    protected $productTaxRepository;

    public function __construct(ProductTaxRepositoryInterface $productTaxRepository)
    {
        $this->productTaxRepository = $productTaxRepository;
    }

    /**
     * Get all taxes for a product
     */
    public function getProductTaxes(int $productId): Collection
    {
        return $this->productTaxRepository->getProductTaxes($productId);
    }

    /**
     * Assign a tax to a product
     */
    public function assignTax(int $productId, array $taxData)
    {
        return $this->productTaxRepository->assignTaxToProduct($productId, $taxData);
    }

    /**
     * Update a product's tax
     */
    public function updateTax(int $productId, int $taxId, array $taxData)
    {
        return $this->productTaxRepository->updateProductTax($productId, $taxId, $taxData);
    }

    /**
     * Remove a tax from a product
     */
    public function removeTax(int $productId, int $taxId): bool
    {
        return $this->productTaxRepository->removeProductTax($productId, $taxId);
    }

    /**
     * Get tax history for a product
     */
    public function getTaxHistory(int $productId): Collection
    {
        return $this->productTaxRepository->getProductTaxHistory($productId);
    }

    /**
     * Get applicable taxes for a product at a specific date
     */
    public function getApplicableTaxes(int $productId, string $date = null): Collection
    {
        return $this->productTaxRepository->getApplicableTaxes($productId, $date);
    }

    /**
     * Calculate taxes for a product
     */
    public function calculateTaxes(int $productId, float $baseAmount, string $date = null): array
    {
        $applicableTaxes = $this->getApplicableTaxes($productId, $date);
        $totalTaxAmount = 0;
        $taxBreakdown = [];

        foreach ($applicableTaxes as $productTax) {
            if ($productTax->is_exempt) {
                $taxBreakdown[] = [
                    'tax_id' => $productTax->tax_id,
                    'tax_name' => $productTax->tax->name,
                    'rate' => 0,
                    'amount' => 0,
                    'is_exempt' => true,
                    'exemption_reason' => $productTax->exemption_reason
                ];
                continue;
            }

            $rate = $productTax->rate ?? $productTax->tax->rate;
            $taxAmount = $baseAmount * ($rate / 100);
            $totalTaxAmount += $taxAmount;

            $taxBreakdown[] = [
                'tax_id' => $productTax->tax_id,
                'tax_name' => $productTax->tax->name,
                'rate' => $rate,
                'amount' => round($taxAmount, 2),
                'is_exempt' => false,
                'exemption_reason' => null
            ];
        }

        return [
            'base_amount' => $baseAmount,
            'total_tax_amount' => round($totalTaxAmount, 2),
            'total_amount' => round($baseAmount + $totalTaxAmount, 2),
            'tax_breakdown' => $taxBreakdown
        ];
    }

    /**
     * Validate tax exemption
     */
    public function validateExemption(array $exemptionData): bool
    {
        // Implement validation logic for tax exemptions
        // This could include:
        // - Checking if the exemption reason is valid
        // - Verifying supporting documentation
        // - Checking if the product category allows exemptions
        // - Validating against tax regulations
        return true;
    }

    /**
     * Get tax summary for reporting
     */
    public function getTaxSummary(int $productId, array $dateRange): array
    {
        $history = $this->getTaxHistory($productId);
        
        $summary = [
            'total_taxes' => $history->count(),
            'current_taxes' => $this->getApplicableTaxes($productId)->count(),
            'exemptions' => $history->where('is_exempt', true)->count(),
            'tax_changes' => $history->count() - 1,
            'tax_periods' => []
        ];

        // Group by effective periods
        $history->each(function ($record) use (&$summary) {
            $period = [
                'from' => $record->effective_from,
                'to' => $record->effective_to ?? 'current',
                'tax_name' => $record->tax->name,
                'rate' => $record->rate,
                'is_exempt' => $record->is_exempt,
                'exemption_reason' => $record->exemption_reason
            ];
            
            $summary['tax_periods'][] = $period;
        });

        return $summary;
    }
}
