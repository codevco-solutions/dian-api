<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\ProductTax;
use App\Repositories\Contracts\Product\ProductTaxRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductTaxRepository extends BaseRepository implements ProductTaxRepositoryInterface
{
    public function __construct(ProductTax $model)
    {
        parent::__construct($model);
    }

    public function getProductTaxes(int $productId): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->with(['tax'])
            ->get();
    }

    public function assignTaxToProduct(int $productId, array $taxData): ProductTax
    {
        // Validate if tax is already assigned
        $existingTax = $this->model
            ->where('product_id', $productId)
            ->where('tax_id', $taxData['tax_id'])
            ->first();

        if ($existingTax) {
            throw new \Exception('Tax is already assigned to this product');
        }

        return $this->model->create([
            'product_id' => $productId,
            'tax_id' => $taxData['tax_id'],
            'rate' => $taxData['rate'] ?? null,
            'is_exempt' => $taxData['is_exempt'] ?? false,
            'exemption_reason' => $taxData['exemption_reason'] ?? null,
            'effective_from' => $taxData['effective_from'] ?? Carbon::now(),
            'effective_to' => $taxData['effective_to'] ?? null,
            'notes' => $taxData['notes'] ?? null
        ]);
    }

    public function updateProductTax(int $productId, int $taxId, array $taxData): ProductTax
    {
        $productTax = $this->model
            ->where('product_id', $productId)
            ->where('tax_id', $taxId)
            ->firstOrFail();

        // If updating rate or exemption status, create historical record
        if (isset($taxData['rate']) || isset($taxData['is_exempt'])) {
            $this->createHistoricalRecord($productTax);
        }

        $productTax->update($taxData);

        return $productTax->fresh();
    }

    public function removeProductTax(int $productId, int $taxId): bool
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('tax_id', $taxId)
            ->delete();
    }

    public function getProductTaxHistory(int $productId): Collection
    {
        return $this->model
            ->withTrashed()
            ->where('product_id', $productId)
            ->with(['tax'])
            ->orderBy('effective_from', 'desc')
            ->get();
    }

    public function getApplicableTaxes(int $productId, string $date = null): Collection
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        return $this->model
            ->where('product_id', $productId)
            ->where(function ($query) use ($date) {
                $query->where(function ($q) use ($date) {
                    $q->where('effective_from', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $date);
                        });
                });
            })
            ->with(['tax'])
            ->get();
    }

    protected function createHistoricalRecord(ProductTax $productTax): void
    {
        // Create a copy of the current record with deleted_at
        $historicalRecord = $productTax->replicate();
        $historicalRecord->effective_to = Carbon::now();
        $historicalRecord->save();
        $historicalRecord->delete();
    }
}
