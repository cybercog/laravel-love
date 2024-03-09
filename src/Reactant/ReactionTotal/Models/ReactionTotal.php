<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love\Reactant\ReactionTotal\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalInterface;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Support\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReactionTotal extends Model implements
    ReactionTotalInterface
{
    use HasFactory;

    public const COUNT_DEFAULT = 0;

    public const WEIGHT_DEFAULT = 0.0;

    protected $table = 'love_reactant_reaction_totals';

    protected static $unguarded = true;

    /**
     * @var array<string, int|float>
     */
    protected $attributes = [
        'count' => self::COUNT_DEFAULT,
        'weight' => self::WEIGHT_DEFAULT,
    ];

    public function count(): Attribute
    {
        return Attribute::make(
            get: fn (int | null $value) => $value ?? self::COUNT_DEFAULT,
            set: fn (int | null $value) => $value ?? self::COUNT_DEFAULT,
        );
    }

    public function weight(): Attribute
    {
        return Attribute::make(
            get: fn (float | null $value) => $value ?? self::WEIGHT_DEFAULT,
            set: fn (float | null $value) => $value ?? self::WEIGHT_DEFAULT,
        );
    }

    public function reactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'reactant_id');
    }

    public function getReactant(): ReactantInterface
    {
        return $this->getAttribute('reactant');
    }

    public function getCount(): int
    {
        return $this->getAttributeValue('count');
    }

    public function getWeight(): float
    {
        return $this->getAttributeValue('weight');
    }

    public function incrementCount(
        int $amount,
    ): void {
        $this->increment('count', $amount);
    }

    public function decrementCount(
        int $amount,
    ): void {
        $this->decrement('count', $amount);
    }

    public function incrementWeight(
        float $amount,
    ): void {
        $this->increment('weight', $amount);
    }

    public function decrementWeight(
        float $amount,
    ): void {
        $this->decrement('weight', $amount);
    }
}
