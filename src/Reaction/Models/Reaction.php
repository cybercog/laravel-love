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

namespace Cog\Laravel\Love\Reaction\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterInterface;
use Cog\Contracts\Love\Reaction\Exceptions\RateInvalid;
use Cog\Contracts\Love\Reaction\Exceptions\RateOutOfRange;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Laravel\Love\Support\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Reaction extends Model implements
    ReactionInterface
{
    public const RATE_DEFAULT = 1.0;

    public const RATE_MIN = 0.01;

    public const RATE_MAX = 99.99;

    protected $table = 'love_reactions';

    /**
     * @var float[]
     */
    protected $attributes = [
        'rate' => self::RATE_DEFAULT,
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'reactant_id',
        'reaction_type_id',
        'rate',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'string',
        'rate' => 'float',
    ];

    public function reactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'reactant_id');
    }

    public function reacter(): BelongsTo
    {
        return $this->belongsTo(Reacter::class, 'reacter_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ReactionType::class, 'reaction_type_id');
    }

    public function getId(): string
    {
        return $this->getAttributeValue('id');
    }

    public function getReactant(): ReactantInterface
    {
        return $this->getAttribute('reactant');
    }

    public function getReacter(): ReacterInterface
    {
        return $this->getAttribute('reacter');
    }

    public function getType(): ReactionTypeInterface
    {
        return $this->getAttribute('type');
    }

    public function getRate(): float
    {
        return $this->getAttributeValue('rate');
    }

    public function getWeight(): float
    {
        return $this->getType()->getMass() * $this->getRate();
    }

    public function setRateAttribute(
        ?float $rate,
    ): void {
        if ($rate !== null && ($rate < self::RATE_MIN || $rate > self::RATE_MAX)) {
            throw RateOutOfRange::withValueBetween($rate, self::RATE_MIN, self::RATE_MAX);
        }

        $this->attributes['rate'] = $rate;
    }

    public function isOfType(
        ReactionTypeInterface $reactionType,
    ): bool {
        return $this->getType()->isEqualTo($reactionType);
    }

    public function isNotOfType(
        ReactionTypeInterface $reactionType,
    ): bool {
        return $this->getType()->isNotEqualTo($reactionType);
    }

    public function isToReactant(
        ReactantInterface $reactant,
    ): bool {
        return $this->getReactant()->isEqualTo($reactant);
    }

    public function isNotToReactant(
        ReactantInterface $reactant,
    ): bool {
        return !$this->isToReactant($reactant);
    }

    public function isByReacter(
        ReacterInterface $reacter,
    ): bool {
        return $this->getReacter()->isEqualTo($reacter);
    }

    public function isNotByReacter(
        ReacterInterface $reacter,
    ): bool {
        return !$this->isByReacter($reacter);
    }

    public function changeRate(
        float $rate,
    ): void {
        if ($this->getRate() === $rate) {
            throw RateInvalid::withSameValue($rate);
        }

        $this->setAttribute('rate', $rate);
        $this->save();
    }
}
