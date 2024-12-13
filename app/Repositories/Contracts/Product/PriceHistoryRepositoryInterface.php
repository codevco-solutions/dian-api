<?php

namespace App\Repositories\Contracts\Product;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface PriceHistoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getHistoryByProduct(int $productId, array $filters = []);
    public function getHistoryByPriceList(int $priceListId, array $filters = []);
    public function recordPriceChange(array $data);
    public function getPriceEvolution(int $productId, int $priceListId, array $dateRange);
}
