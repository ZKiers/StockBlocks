<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use App\Services\StocksInfoService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListStocks extends ListRecords
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetch')
                ->label('Search & add stocks')
                ->icon('heroicon-o-magnifying-glass')
                ->schema([
                    TextInput::make('query')
                        ->label('Symbol or company name')
                        ->placeholder('e.g. AAPL or Apple')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $response = app(StocksInfoService::class)->symbolLookup($data['query']);

                    $count = $response['count'] ?? 0;

                    Notification::make()
                        ->title($count > 0
                            ? "Found and saved {$count} result(s)"
                            : 'No matching stocks found')
                        ->status($count > 0 ? 'success' : 'warning')
                        ->send();
                }),
        ];
    }
}
