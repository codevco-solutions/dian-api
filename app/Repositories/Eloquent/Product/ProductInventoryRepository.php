<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\ProductInventory;
use App\Models\Product\InventoryMovement;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\Product\ProductInventoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductInventoryRepository extends BaseRepository implements ProductInventoryRepositoryInterface
{
    public function __construct(ProductInventory $model)
    {
        parent::__construct($model);
    }

    public function getByProductAndBranch(int $productId, int $branchId)
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->first();
    }

    public function updateStock(int $productId, int $branchId, float $quantity)
    {
        return DB::transaction(function () use ($productId, $branchId, $quantity) {
            $inventory = $this->getByProductAndBranch($productId, $branchId);
            
            if (!$inventory) {
                $inventory = $this->model->create([
                    'product_id' => $productId,
                    'branch_id' => $branchId,
                    'quantity' => $quantity
                ]);
            } else {
                $inventory->update(['quantity' => $quantity]);
            }

            return $inventory;
        });
    }

    public function getMovements(int $productId, int $branchId, array $filters = [])
    {
        $query = InventoryMovement::query()
            ->where('product_id', $productId)
            ->where('branch_id', $branchId);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    public function createMovement(array $data)
    {
        return DB::transaction(function () use ($data) {
            $inventory = $this->getByProductAndBranch($data['product_id'], $data['branch_id']);
            
            if (!$inventory) {
                $inventory = $this->model->create([
                    'product_id' => $data['product_id'],
                    'branch_id' => $data['branch_id'],
                    'quantity' => 0
                ]);
            }

            $previousStock = $inventory->quantity;
            $newStock = $previousStock;

            switch ($data['type']) {
                case 'entrada':
                    $newStock += $data['quantity'];
                    break;
                case 'salida':
                    $newStock -= $data['quantity'];
                    break;
                case 'ajuste':
                    $newStock = $data['quantity'];
                    break;
            }

            $inventory->update(['quantity' => $newStock]);

            return InventoryMovement::create([
                'product_id' => $data['product_id'],
                'branch_id' => $data['branch_id'],
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id()
            ]);
        });
    }

    public function getStockAlerts(int $branchId)
    {
        return $this->model
            ->where('branch_id', $branchId)
            ->whereRaw('quantity <= reorder_point')
            ->with('product')
            ->get();
    }
}
