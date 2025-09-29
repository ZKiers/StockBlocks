<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use App\Services\StocksInfoService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListStocks extends ListRecords
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetch')
                ->schema([
                    TextInput::make('query')->required(),
                ])
                ->action(function (array $data) {
                    $service = new StocksInfoService();
                    $service->symbolLookup($data['query']);
                })
        ];
    }
}
