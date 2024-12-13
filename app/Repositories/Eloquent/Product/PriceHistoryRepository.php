<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\PriceHistory;
use App\Repositories\Contracts\Product\PriceHistoryRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PriceHistoryRepository extends BaseRepository implements PriceHistoryRepositoryInterface
{
    public function __construct(PriceHistory $model)
    {
        parent::__construct($model);
    }

    public function getHistoryByProduct(int $productId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('product_id', $productId);

        if (isset($filters['price_list_id'])) {
            $query->where('price_list_id', $filters['price_list_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('effective_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('effective_date', '<=', $filters['date_to']);
        }

        return $query->with(['priceList'])
            ->orderBy('effective_date', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getHistoryByPriceList(int $priceListId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('price_list_id', $priceListId);

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('effective_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('effective_date', '<=', $filters['date_to']);
        }

        return $query->with(['product'])
            ->orderBy('effective_date', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function recordPriceChange(array $data): PriceHistory
    {
        // Set effective date to now if not provided
        $data['effective_date'] = $data['effective_date'] ?? Carbon::now();
        
        // Get previous price if exists
        $previousPrice = $this->model
            ->where('product_id', $data['product_id'])
            ->where('price_list_id', $data['price_list_id'])
            ->where('effective_date', '<', $data['effective_date'])
            ->orderBy('effective_date', 'desc')
            ->first();

        if ($previousPrice) {
            $data['previous_price'] = $previousPrice->price;
            $data['price_change'] = $data['price'] - $previousPrice->price;
            $data['price_change_percentage'] = ($data['price_change'] / $previousPrice->price) * 100;
        }

        return $this->model->create($data);
    }

    public function getPriceEvolution(int $productId, int $priceListId, array $dateRange): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('price_list_id', $priceListId)
            ->whereBetween('effective_date', [$dateRange['start'], $dateRange['end']])
            ->orderBy('effective_date', 'asc')
            ->get();
    }
}
