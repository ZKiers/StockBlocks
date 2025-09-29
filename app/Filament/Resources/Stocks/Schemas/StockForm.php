<?php

namespace App\Filament\Resources\Stocks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->required(),
                TextInput::make('display_symbol')
                    ->required(),
                TextInput::make('symbol')
                    ->required(),
                TextInput::make('type')
                    ->required(),
            ]);
    }
}
