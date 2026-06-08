<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Your holdings
        </x-slot>

        <x-slot name="description">
            Live overview of the shares you currently own
        </x-slot>

        <div wire:poll.15s class="overflow-x-auto">
            @if ($holdings->isEmpty())
                <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    You don't own any shares yet. Add a <strong>buy</strong> transaction to get started.
                </div>
            @else
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="px-3 py-2">Symbol</th>
                            <th class="px-3 py-2 text-right">Shares</th>
                            <th class="px-3 py-2 text-right">Avg. cost</th>
                            <th class="px-3 py-2 text-right">Price</th>
                            <th class="px-3 py-2 text-right">Market value</th>
                            <th class="px-3 py-2 text-right">Unrealized P/L</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($holdings as $holding)
                            @php($plPositive = $holding['unrealized_pl'] >= 0)
                            <tr>
                                <td class="px-3 py-2">
                                    <span class="font-semibold text-gray-950 dark:text-white">{{ $holding['symbol'] }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($holding['description'], 30) }}</span>
                                </td>
                                <td class="px-3 py-2 text-right tabular-nums">{{ number_format($holding['quantity']) }}</td>
                                <td class="px-3 py-2 text-right tabular-nums">${{ number_format($holding['average_price'], 2) }}</td>
                                <td class="px-3 py-2 text-right tabular-nums">${{ number_format($holding['current_price'], 2) }}</td>
                                <td class="px-3 py-2 text-right tabular-nums font-medium text-gray-950 dark:text-white">${{ number_format($holding['market_value'], 2) }}</td>
                                <td class="px-3 py-2 text-right tabular-nums font-medium {{ $plPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $plPositive ? '+' : '-' }}${{ number_format(abs($holding['unrealized_pl']), 2) }}
                                    <span class="block text-xs">{{ sprintf('%+.2f%%', $holding['unrealized_pl_percent']) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
