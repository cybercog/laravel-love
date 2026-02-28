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

namespace Cog\Laravel\Love\Reactant\ReactionCounter\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Laravel\Love\Support\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReactionCounter extends Model implements
    ReactionCounterInterface
{
    use HasFactory;

    public const COUNT_DEFAULT = 0;

    public const WEIGHT_DEFAULT = 0.0;

    protected $table = 'love_reactant_reaction_counters';

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
        return new Attribute(
            get: fn (?int $value) => $value ?? self::COUNT_DEFAULT,
            set: fn (?int $value) => $value ?? self::COUNT_DEFAULT,
        );
    }

    public function weight(): Attribute
    {
        return new Attribute(
            get: fn (?float $value) => $value ?? self::WEIGHT_DEFAULT,
            set: fn (?float $value) => $value ?? self::WEIGHT_DEFAULT,
        );
    }

    public function reactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'reactant_id');
    }

    public function reactionType(): BelongsTo
    {
        return $this->belongsTo(ReactionType::class, 'reaction_type_id');
    }

    public function getReactant(): ReactantInterface
    {
        return $this->getAttribute('reactant');
    }

    public function getReactionType(): ReactionTypeInterface
    {
        return $this->getAttribute('reactionType');
    }

    public function isReactionOfType(
        ReactionTypeInterface $reactionType,
    ): bool {
        return $this->getReactionType()->isEqualTo($reactionType);
    }

    public function isNotReactionOfType(
        ReactionTypeInterface $reactionType,
    ): bool {
        return !$this->isReactionOfType($reactionType);
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
