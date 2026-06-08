<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioService;
use Filament\Widgets\Widget;

class HoldingsOverview extends Widget
{
    protected string $view = 'filament.widgets.holdings-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    protected function getViewData(): array
    {
        return [
            'holdings' => app(PortfolioService::class)->holdings(auth()->user()),
        ];
    }
}
