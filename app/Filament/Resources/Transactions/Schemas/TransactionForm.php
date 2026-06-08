<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        $recalculateTotal = function (Get $get, Set $set): void {
            $quantity = (float) $get('quantity');
            $price = (float) $get('price');
            $set('total', round($quantity * $price, 4));
        };

        return $schema
            ->components([
                Select::make('stock_id')
                    ->label('Stock')
                    ->relationship('stock', 'display_symbol')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->display_symbol} — {$record->description}")
                    ->searchable(['display_symbol', 'symbol', 'description'])
                    ->preload()
                    ->required(),

                Select::make('type')
                    ->options([
                        'buy' => 'Buy',
                        'sell' => 'Sell',
                    ])
                    ->default('buy')
                    ->required(),

                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(1)
                    ->step(1)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated($recalculateTotal),

                TextInput::make('price')
                    ->label('Price per share')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated($recalculateTotal),

                TextInput::make('total')
                    ->numeric()
                    ->prefix('$')
                    ->readOnly()
                    ->dehydrated()
                    ->helperText('Automatically calculated from quantity × price.'),

                DatePicker::make('transaction_date')
                    ->default(now())
                    ->required(),
            ]);
    }
}
