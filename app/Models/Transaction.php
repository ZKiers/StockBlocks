<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $stock_id
 * @property string $type
 * @property int $quantity
 * @property float $price
 * @property float $total
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Stock $stock
 */
class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'stock_id',
        'type',
        'quantity',
        'price',
        'total',
        'transaction_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:4',
        'total' => 'decimal:4',
        'transaction_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
