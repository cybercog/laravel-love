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

namespace Cog\Laravel\Love\Reactant\Models;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Contracts\Love\Reactant\Exceptions\NotAssignedToReactable;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterDuplicate;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterInterface;
use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalDuplicate;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalInterface;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterInterface;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\Support\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Reactant extends Model implements
    ReactantInterface
{
    protected $table = 'love_reactants';

    /**
     * @var string[]
     */
    protected $fillable = [
        'type',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'string',
    ];

    public function reactable(): MorphTo
    {
        return $this->morphTo('reactable', 'type', 'id', 'love_reactant_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reactant_id');
    }

    public function reactionCounters(): HasMany
    {
        return $this->hasMany(ReactionCounter::class, 'reactant_id');
    }

    public function reactionTotal(): HasOne
    {
        return $this->hasOne(ReactionTotal::class, 'reactant_id');
    }

    public function getId(): string
    {
        return $this->getAttributeValue('id');
    }

    public function getReactable(): ReactableInterface
    {
        $reactable = $this->getAttribute('reactable');

        if ($reactable === null) {
            throw new NotAssignedToReactable();
        }

        return $reactable;
    }

    public function getReactions(): iterable
    {
        return $this->getAttribute('reactions');
    }

    public function getReactionCounters(): iterable
    {
        return $this->getAttribute('reactionCounters');
    }

    public function getReactionCounterOfType(
        ReactionTypeInterface $reactionType
    ): ReactionCounterInterface {
        // TODO: Test query count with eager loaded relation
        // TODO: Test query count without eager loaded relation
        $counter = $this
            ->getAttribute('reactionCounters')
            ->where('reaction_type_id', $reactionType->getId())
            ->first();

        if ($counter === null) {
            return new NullReactionCounter($this, $reactionType);
        }

        return $counter;
    }

    public function getReactionTotal(): ReactionTotalInterface
    {
        return $this->getAttribute('reactionTotal')
            ?? new NullReactionTotal($this);
    }

    public function isReactedBy(
        ReacterInterface $reacter,
        ?ReactionTypeInterface $reactionType = null,
        ?float $rate = null
    ): bool {
        if ($reacter->isNull()) {
            return false;
        }

        // TODO: Test if relation was loaded partially
        if ($this->relationLoaded('reactions')) {
            return $this
                ->getAttribute('reactions')
                ->contains(function (ReactionInterface $reaction) use ($reacter, $reactionType, $rate) {
                    if ($reaction->isNotByReacter($reacter)) {
                        return false;
                    }

                    if ($reactionType !== null && $reaction->isNotOfType($reactionType)) {
                        return false;
                    }

                    if ($rate !== null && $reaction->getRate() !== $rate) {
                        return false;
                    }

                    return true;
                });
        }

        $query = $this->reactions()->where('reacter_id', $reacter->getId());

        if ($reactionType !== null) {
            $query->where('reaction_type_id', $reactionType->getId());
        }

        if ($rate !== null) {
            $query->where('rate', $rate);
        }

        return $query->exists();
    }

    public function isNotReactedBy(
        ReacterInterface $reacter,
        ?ReactionTypeInterface $reactionType = null,
        ?float $rate = null
    ): bool {
        return !$this->isReactedBy($reacter, $reactionType, $rate);
    }

    public function isEqualTo(
        ReactantInterface $that
    ): bool {
        return $that->isNotNull()
            && $this->getId() === $that->getId();
    }

    public function isNotEqualTo(
        ReactantInterface $that
    ): bool {
        return !$this->isEqualTo($that);
    }

    public function isNull(): bool
    {
        return !$this->exists;
    }

    public function isNotNull(): bool
    {
        return $this->exists;
    }

    public function createReactionCounterOfType(
        ReactionTypeInterface $reactionType
    ): void {
        if ($this->reactionCounters()->where('reaction_type_id', $reactionType->getId())->exists()) {
            throw ReactionCounterDuplicate::ofTypeForReactant($reactionType, $this);
        }

        $this->reactionCounters()->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        // Need to reload relation with fresh data
        $this->load('reactionCounters');
    }

    public function createReactionTotal(): void
    {
        if ($this->reactionTotal()->exists()) {
            throw ReactionTotalDuplicate::forReactant($this);
        }

        $this->reactionTotal()->create();

        // Need to reload relation with fresh data
        $this->load('reactionTotal');
    }
}
