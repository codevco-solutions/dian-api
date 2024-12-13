<?php

namespace App\Repositories\Contracts\Product;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface ProductTaxRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductTaxes(int $productId);
    public function assignTaxToProduct(int $productId, array $taxData);
    public function updateProductTax(int $productId, int $taxId, array $taxData);
    public function removeProductTax(int $productId, int $taxId);
    public function getProductTaxHistory(int $productId);
    public function getApplicableTaxes(int $productId, string $date = null);
}
