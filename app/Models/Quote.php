<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $stock_id
 * @property float $price
 * @property float|null $change
 * @property float|null $percent_change
 * @property float|null $daily_high
 * @property float|null $daily_low
 * @property float|null $open
 * @property float|null $previous_close
 * @property string $timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Stock|null $stock
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereDailyHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereDailyLow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote wherePercentChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote wherePreviousClose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereStockId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Quote extends Model
{
    protected $guarded = [];

    public function stock() {
        return $this->belongsTo(Stock::class);
    }
}
