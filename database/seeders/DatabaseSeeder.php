<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ],
        );

        $stocks = [
            ['symbol' => 'AAPL', 'display_symbol' => 'AAPL', 'description' => 'Apple Inc', 'type' => 'Common Stock'],
            ['symbol' => 'MSFT', 'display_symbol' => 'MSFT', 'description' => 'Microsoft Corp', 'type' => 'Common Stock'],
            ['symbol' => 'GOOGL', 'display_symbol' => 'GOOGL', 'description' => 'Alphabet Inc', 'type' => 'Common Stock'],
            ['symbol' => 'AMZN', 'display_symbol' => 'AMZN', 'description' => 'Amazon.com Inc', 'type' => 'Common Stock'],
            ['symbol' => 'TSLA', 'display_symbol' => 'TSLA', 'description' => 'Tesla Inc', 'type' => 'Common Stock'],
            ['symbol' => 'NVDA', 'display_symbol' => 'NVDA', 'description' => 'NVIDIA Corp', 'type' => 'Common Stock'],
        ];

        foreach ($stocks as $stock) {
            Stock::updateOrCreate(['symbol' => $stock['symbol']], $stock);
        }

        // A few example buy transactions so the portfolio is populated.
        $transactions = [
            ['symbol' => 'AAPL', 'quantity' => 10, 'price' => 175.50, 'days_ago' => 120],
            ['symbol' => 'AAPL', 'quantity' => 5, 'price' => 190.00, 'days_ago' => 40],
            ['symbol' => 'MSFT', 'quantity' => 8, 'price' => 320.00, 'days_ago' => 90],
            ['symbol' => 'GOOGL', 'quantity' => 12, 'price' => 135.25, 'days_ago' => 60],
            ['symbol' => 'NVDA', 'quantity' => 15, 'price' => 110.00, 'days_ago' => 30],
            ['symbol' => 'TSLA', 'quantity' => 6, 'price' => 240.00, 'days_ago' => 15],
        ];

        foreach ($transactions as $transaction) {
            $stock = Stock::where('symbol', $transaction['symbol'])->first();

            if (! $stock) {
                continue;
            }

            Transaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'stock_id' => $stock->id,
                    'transaction_date' => now()->subDays($transaction['days_ago'])->toDateString(),
                ],
                [
                    'type' => 'buy',
                    'quantity' => $transaction['quantity'],
                    'price' => $transaction['price'],
                    'total' => round($transaction['quantity'] * $transaction['price'], 4),
                ],
            );
        }
    }
}
