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

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Support\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReactionTotal extends Model implements
    ReactionTotalContract
{
    const DEFAULT_COUNT = 0;

    const DEFAULT_WEIGHT = 0;

    protected $table = 'love_reactant_reaction_totals';

    protected $attributes = [
        'count' => self::DEFAULT_COUNT,
        'weight' => self::DEFAULT_WEIGHT,
    ];

    protected $fillable = [
        'count',
        'weight',
    ];

    protected $casts = [
        'count' => 'integer',
        'weight' => 'integer',
    ];

    public function reactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'reactant_id');
    }

    public function getReactant(): ReactantContract
    {
        return $this->getAttribute('reactant');
    }

    public function getCount(): int
    {
        return $this->getAttributeValue('count');
    }

    public function getWeight(): int
    {
        return $this->getAttributeValue('weight');
    }

    public function incrementCount(
        int $amount
    ): void {
        $this->increment('count', $amount);
    }

    public function decrementCount(
        int $amount
    ): void {
        $this->decrement('count', $amount);
    }

    public function incrementWeight(
        int $amount
    ): void {
        $this->increment('weight', $amount);
    }

    public function decrementWeight(
        int $amount
    ): void {
        $this->decrement('weight', $amount);
    }
}
