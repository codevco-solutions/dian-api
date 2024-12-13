<?php

namespace App\Repositories\Contracts\Product;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface ProductInventoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getByProductAndBranch(int $productId, int $branchId);
    public function updateStock(int $productId, int $branchId, float $quantity);
    public function getMovements(int $productId, int $branchId, array $filters = []);
    public function createMovement(array $data);
    public function getStockAlerts(int $branchId);
}
