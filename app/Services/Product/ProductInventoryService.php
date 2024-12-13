<?php

namespace App\Services\Product;

use App\Repositories\Contracts\Product\ProductInventoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ProductInventoryService
{
    protected $repository;

    public function __construct(ProductInventoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getInventory(int $productId, int $branchId)
    {
        return $this->repository->getByProductAndBranch($productId, $branchId);
    }

    public function updateStock(int $productId, int $branchId, float $quantity, array $movementData = [])
    {
        // Crear el movimiento de inventario
        $movement = array_merge([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'type' => 'ajuste',
            'quantity' => $quantity,
        ], $movementData);

        return $this->repository->createMovement($movement);
    }

    public function addStock(int $productId, int $branchId, float $quantity, array $movementData = [])
    {
        $movement = array_merge([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'type' => 'entrada',
            'quantity' => abs($quantity),
        ], $movementData);

        return $this->repository->createMovement($movement);
    }

    public function removeStock(int $productId, int $branchId, float $quantity, array $movementData = [])
    {
        $inventory = $this->getInventory($productId, $branchId);
        
        // Verificar si hay suficiente stock
        if ($inventory && $inventory->quantity < $quantity) {
            throw new \Exception('No hay suficiente stock disponible');
        }

        $movement = array_merge([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'type' => 'salida',
            'quantity' => abs($quantity),
        ], $movementData);

        return $this->repository->createMovement($movement);
    }

    public function getMovements(int $productId, int $branchId, array $filters = [])
    {
        return $this->repository->getMovements($productId, $branchId, $filters);
    }

    public function getStockAlerts(int $branchId)
    {
        return $this->repository->getStockAlerts($branchId);
    }

    public function transferStock(int $productId, int $fromBranchId, int $toBranchId, float $quantity, string $notes = null)
    {
        // Verificar stock en sucursal origen
        $sourceInventory = $this->getInventory($productId, $fromBranchId);
        if (!$sourceInventory || $sourceInventory->quantity < $quantity) {
            throw new \Exception('No hay suficiente stock en la sucursal origen');
        }

        // Crear movimiento de salida en sucursal origen
        $this->removeStock($productId, $fromBranchId, $quantity, [
            'type' => 'traslado',
            'notes' => $notes ?? 'Traslado a sucursal ' . $toBranchId
        ]);

        // Crear movimiento de entrada en sucursal destino
        return $this->addStock($productId, $toBranchId, $quantity, [
            'type' => 'traslado',
            'notes' => $notes ?? 'Traslado desde sucursal ' . $fromBranchId
        ]);
    }
}
