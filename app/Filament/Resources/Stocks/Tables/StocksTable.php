<?php

namespace App\Filament\Resources\Stocks\Tables;

use App\Models\Stock;
use App\Services\StocksInfoService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('latestQuote'))
            ->poll('30s')
            ->columns([
                TextColumn::make('display_symbol')
                    ->label('Symbol')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('price')
                    ->money('USD')
                    ->placeholder('No quote yet')
                    ->sortable(),
                TextColumn::make('percent_change')
                    ->label('Change %')
                    ->placeholder('—')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        (float) $state > 0 => 'success',
                        (float) $state < 0 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => $state === null
                        ? '—'
                        : sprintf('%+.2f%%', (float) $state)),
                TextColumn::make('latestQuote.timestamp')
                    ->label('Last quote')
                    ->since()
                    ->placeholder('Never')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('get_quote')
                    ->label('Refresh quote')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Stock $record) {
                        $quote = app(StocksInfoService::class)->quote($record);

                        if ($quote) {
                            Notification::make()
                                ->title("Updated {$record->display_symbol}")
                                ->body('Latest price: $'.number_format((float) $quote->price, 2))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title("Could not fetch a quote for {$record->display_symbol}")
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('refresh_quotes')
                        ->label('Refresh quotes')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Collection $records) {
                            $updated = app(StocksInfoService::class)->quoteMany($records);

                            Notification::make()
                                ->title("Refreshed {$updated} quote(s)")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
