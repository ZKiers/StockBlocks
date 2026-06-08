<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortfolioStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '15s';

    protected ?string $heading = 'Portfolio';

    protected function getStats(): array
    {
        $summary = app(PortfolioService::class)->summary(auth()->user());

        $plPositive = $summary['unrealized_pl'] >= 0;
        $dayPositive = $summary['day_change'] >= 0;

        return [
            Stat::make('Portfolio value', '$'.number_format($summary['market_value'], 2))
                ->description($summary['holdings_count'].' holding(s)')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            Stat::make('Total invested', '$'.number_format($summary['cost_basis'], 2))
                ->description('Cost basis of current holdings')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray'),

            Stat::make('Unrealized P/L', sprintf('%s$%s', $plPositive ? '+' : '-', number_format(abs($summary['unrealized_pl']), 2)))
                ->description(sprintf('%+.2f%% overall', $summary['unrealized_pl_percent']))
                ->descriptionIcon($plPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($plPositive ? 'success' : 'danger'),

            Stat::make("Today's change", sprintf('%s$%s', $dayPositive ? '+' : '-', number_format(abs($summary['day_change']), 2)))
                ->description(sprintf('%+.2f%% today', $summary['day_change_percent']))
                ->descriptionIcon($dayPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($dayPositive ? 'success' : 'danger'),
        ];
    }
}
