<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class PortfolioService
{
    /**
     * Build the current holdings for a user, aggregated per stock.
     *
     * Uses the average-cost method to determine the cost basis of the
     * shares that are still held.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function holdings(User $user): Collection
    {
        return $user->transactions()
            ->with('stock.latestQuote')
            ->get()
            ->groupBy('stock_id')
            ->map(function (Collection $transactions) {
                $stock = $transactions->first()->stock;

                $boughtQuantity = (float) $transactions->where('type', 'buy')->sum('quantity');
                $boughtCost = (float) $transactions->where('type', 'buy')->sum('total');
                $soldQuantity = (float) $transactions->where('type', 'sell')->sum('quantity');

                $netQuantity = $boughtQuantity - $soldQuantity;
                $averagePrice = $boughtQuantity > 0 ? $boughtCost / $boughtQuantity : 0.0;
                $costBasis = $averagePrice * $netQuantity;

                $currentPrice = (float) ($stock?->price ?? 0);
                $marketValue = $currentPrice * $netQuantity;

                $perShareChange = (float) ($stock?->latestQuote?->change ?? 0);
                $dayChangeValue = $perShareChange * $netQuantity;

                $unrealizedPl = $marketValue - $costBasis;
                $unrealizedPlPercent = $costBasis > 0 ? ($unrealizedPl / $costBasis) * 100 : 0.0;

                return [
                    'stock' => $stock,
                    'symbol' => $stock?->display_symbol ?? '—',
                    'description' => $stock?->description ?? '',
                    'quantity' => $netQuantity,
                    'average_price' => $averagePrice,
                    'cost_basis' => $costBasis,
                    'current_price' => $currentPrice,
                    'market_value' => $marketValue,
                    'day_change_value' => $dayChangeValue,
                    'unrealized_pl' => $unrealizedPl,
                    'unrealized_pl_percent' => $unrealizedPlPercent,
                ];
            })
            ->filter(fn (array $holding) => $holding['quantity'] > 0)
            ->sortByDesc('market_value')
            ->values();
    }

    /**
     * Aggregate portfolio totals for a user.
     *
     * @return array<string, float|int>
     */
    public function summary(User $user): array
    {
        $holdings = $this->holdings($user);

        $marketValue = (float) $holdings->sum('market_value');
        $costBasis = (float) $holdings->sum('cost_basis');
        $dayChange = (float) $holdings->sum('day_change_value');
        $unrealizedPl = $marketValue - $costBasis;

        return [
            'market_value' => $marketValue,
            'cost_basis' => $costBasis,
            'unrealized_pl' => $unrealizedPl,
            'unrealized_pl_percent' => $costBasis > 0 ? ($unrealizedPl / $costBasis) * 100 : 0.0,
            'day_change' => $dayChange,
            'day_change_percent' => ($marketValue - $dayChange) > 0
                ? ($dayChange / ($marketValue - $dayChange)) * 100
                : 0.0,
            'holdings_count' => $holdings->count(),
        ];
    }
}
