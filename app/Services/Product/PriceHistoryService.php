<?php

namespace App\Services\Product;

use App\Repositories\Contracts\Product\PriceHistoryRepositoryInterface;
use App\Repositories\Contracts\Product\PriceListRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PriceHistoryService
{
    protected $priceHistoryRepository;
    protected $priceListRepository;

    public function __construct(
        PriceHistoryRepositoryInterface $priceHistoryRepository,
        PriceListRepositoryInterface $priceListRepository
    ) {
        $this->priceHistoryRepository = $priceHistoryRepository;
        $this->priceListRepository = $priceListRepository;
    }

    /**
     * Get price history for a product
     */
    public function getProductPriceHistory(int $productId, array $filters = [])
    {
        return $this->priceHistoryRepository->getHistoryByProduct($productId, $filters);
    }

    /**
     * Get price history for a price list
     */
    public function getPriceListHistory(int $priceListId, array $filters = [])
    {
        return $this->priceHistoryRepository->getHistoryByPriceList($priceListId, $filters);
    }

    /**
     * Record a price change
     */
    public function recordPriceChange(int $productId, int $priceListId, float $newPrice, array $data = [])
    {
        $priceData = array_merge($data, [
            'product_id' => $productId,
            'price_list_id' => $priceListId,
            'price' => $newPrice,
            'effective_date' => $data['effective_date'] ?? Carbon::now(),
            'reason' => $data['reason'] ?? 'Price update',
            'user_id' => auth()->id()
        ]);

        return $this->priceHistoryRepository->recordPriceChange($priceData);
    }

    /**
     * Get price evolution over time
     */
    public function getPriceEvolution(int $productId, int $priceListId, string $period = '1month')
    {
        $end = Carbon::now();
        $start = match($period) {
            '1week' => Carbon::now()->subWeek(),
            '1month' => Carbon::now()->subMonth(),
            '3months' => Carbon::now()->subMonths(3),
            '6months' => Carbon::now()->subMonths(6),
            '1year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth()
        };

        return $this->priceHistoryRepository->getPriceEvolution($productId, $priceListId, [
            'start' => $start,
            'end' => $end
        ]);
    }

    /**
     * Analyze price trends
     */
    public function analyzePriceTrends(int $productId, int $priceListId): array
    {
        $history = $this->getPriceEvolution($productId, $priceListId, '6months');
        
        if ($history->isEmpty()) {
            return [
                'trend' => 'stable',
                'changes' => 0,
                'average_price' => 0,
                'min_price' => 0,
                'max_price' => 0,
                'total_variation' => 0,
                'average_variation' => 0
            ];
        }

        $prices = $history->pluck('price');
        $changes = $history->count() - 1;
        $avgPrice = $prices->avg();
        $minPrice = $prices->min();
        $maxPrice = $prices->max();
        
        // Calculate total variation (last price vs first price)
        $totalVariation = $changes > 0 
            ? (($prices->last() - $prices->first()) / $prices->first()) * 100 
            : 0;

        // Calculate average variation between changes
        $variations = collect();
        for ($i = 1; $i < $history->count(); $i++) {
            $previousPrice = $history[$i-1]->price;
            $currentPrice = $history[$i]->price;
            $variations->push((($currentPrice - $previousPrice) / $previousPrice) * 100);
        }
        $avgVariation = $variations->avg() ?? 0;

        // Determine trend
        $trend = match(true) {
            $totalVariation > 5 => 'increasing',
            $totalVariation < -5 => 'decreasing',
            default => 'stable'
        };

        return [
            'trend' => $trend,
            'changes' => $changes,
            'average_price' => round($avgPrice, 2),
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'total_variation' => round($totalVariation, 2),
            'average_variation' => round($avgVariation, 2)
        ];
    }
}
