<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioService;
use Filament\Widgets\ChartWidget;

class PortfolioAllocationChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Portfolio allocation';

    protected ?string $description = 'Current market value per holding';

    protected ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $holdings = app(PortfolioService::class)->holdings(auth()->user());

        if ($holdings->isEmpty()) {
            return [
                'datasets' => [['data' => [1], 'backgroundColor' => ['#e5e7eb']]],
                'labels' => ['No holdings yet'],
            ];
        }

        $palette = [
            '#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6',
            '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16',
        ];

        $labels = $holdings->pluck('symbol')->all();
        $values = $holdings->map(fn (array $h) => round($h['market_value'], 2))->all();
        $colors = collect($labels)
            ->map(fn ($label, $index) => $palette[$index % count($palette)])
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Market value',
                    'data' => $values,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
