<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $description
 * @property string $display_symbol
 * @property string $symbol
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quote> $quotes
 * @property-read int|null $quotes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock whereDisplaySymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stock whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Stock extends Model
{
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * The most recent quote for this stock. Useful for eager loading
     * to avoid N+1 queries when displaying prices in tables.
     */
    public function latestQuote(): HasOne
    {
        return $this->hasOne(Quote::class)->latestOfMany('id');
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('latestQuote')
                ? $this->latestQuote?->price
                : $this->quotes()->latest()->value('price')
        );
    }

    protected function percentChange(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('latestQuote')
                ? $this->latestQuote?->percent_change
                : $this->quotes()->latest()->value('percent_change')
        );
    }
}
