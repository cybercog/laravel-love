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

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reactant\Exceptions\NotAssignedToReactable;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterDuplicate;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalDuplicate;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
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
    ReactantContract
{
    protected $table = 'love_reactants';

    protected $fillable = [
        'type',
    ];

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

    public function getReactable(): ReactableContract
    {
        $reactable = $this->getAttribute('reactable');

        if (is_null($reactable)) {
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
        ReactionTypeContract $reactionType
    ): ReactionCounterContract {
        // TODO: Test query count with eager loaded relation
        // TODO: Test query count without eager loaded relation
        $counter = $this
            ->getAttribute('reactionCounters')
            ->where('reaction_type_id', $reactionType->getId())
            ->first();

        if (is_null($counter)) {
            return new NullReactionCounter($this, $reactionType);
        }

        return $counter;
    }

    public function getReactionTotal(): ReactionTotalContract
    {
        return $this->getAttribute('reactionTotal')
            ?? new NullReactionTotal($this);
    }

    public function isReactedBy(
        ReacterContract $reacter,
        ?ReactionTypeContract $reactionType = null
    ): bool {
        if ($reacter->isNull()) {
            return false;
        }

        // TODO: Test if relation was loaded partially
        if ($this->relationLoaded('reactions')) {
            return $this
                ->getAttribute('reactions')
                ->contains(function (ReactionContract $reaction) use ($reacter, $reactionType) {
                    return is_null($reactionType)
                        ? $reaction->isByReacter($reacter)
                        : $reaction->isByReacter($reacter) && $reaction->isOfType($reactionType);
                });
        }

        $query = $this->reactions()->where('reacter_id', $reacter->getId());

        if (!is_null($reactionType)) {
            $query->where('reaction_type_id', $reactionType->getId());
        }

        return $query->exists();
    }

    public function isNotReactedBy(
        ReacterContract $reacter,
        ?ReactionTypeContract $reactionType = null
    ): bool {
        return !$this->isReactedBy($reacter, $reactionType);
    }

    public function isEqualTo(
        ReactantContract $that
    ): bool {
        return $that->isNotNull()
            && $this->getId() === $that->getId();
    }

    public function isNotEqualTo(
        ReactantContract $that
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
        ReactionTypeContract $reactionType
    ): void {
        if ($this->reactionCounters()->where('reaction_type_id', $reactionType->getId())->exists()) {
            throw ReactionCounterDuplicate::ofTypeForReactant($reactionType, $this);
        }

        $this->reactionCounters()->create([
            'reaction_type_id' => $reactionType->getId(),
            'count' => 0,
            'weight' => 0,
        ]);

        // Need to reload relation with fresh data
        $this->load('reactionCounters');
    }

    public function createReactionTotal(): void
    {
        if ($this->reactionTotal()->exists()) {
            throw ReactionTotalDuplicate::forReactant($this);
        }

        $this->reactionTotal()->create([
            'count' => 0,
            'weight' => 0,
        ]);

        // Need to reload relation with fresh data
        $this->load('reactionTotal');
    }
}
