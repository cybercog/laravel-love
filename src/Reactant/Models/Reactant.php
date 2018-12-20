<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love\Reactant\Models;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Reactant extends Model implements ReactantContract
{
    protected $table = 'love_reactants';

    protected $fillable = [
        'type',
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

    public function getReactable(): ReactableContract
    {
        // TODO: (?) Return `NullReactable` or throw exception `NotAssignedToReactable`?
        return $this->getAttribute('reactable');
    }

    public function getReactions(): iterable
    {
        return $this->getAttribute('reactions');
    }

    public function getReactionCounters(): iterable
    {
        return $this->getAttribute('reactionCounters');
    }

    public function getReactionCounterOfType(ReactionTypeContract $reactionType): ReactionCounterContract
    {
        /** @var null|\Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter $counter */
        $counter = $this
            ->reactionCounters()
            ->where('reaction_type_id', $reactionType->getKey())
            ->first();

        if (is_null($counter)) {
            return new NullReactionCounter($this, $reactionType);
        }

        return $counter;
    }

    public function getReactionTotal(): ReactionTotalContract
    {
        return $this->getAttribute('reactionTotal') ?? new NullReactionTotal($this);
    }

    public function isReactedBy(ReacterContract $reacter): bool
    {
        if ($reacter instanceof NullReacter) {
            return false;
        }

        return $this->reactions()->where([
            'reacter_id' => $reacter->getKey(),
        ])->exists();
    }

    public function isNotReactedBy(ReacterContract $reacter): bool
    {
        return !$this->isReactedBy($reacter);
    }

    public function isReactedByWithType(ReacterContract $reacter, ReactionTypeContract $reactionType): bool
    {
        if ($reacter instanceof NullReacter) {
            return false;
        }

        return $this->reactions()->where([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
        ])->exists();
    }

    public function isNotReactedByWithType(ReacterContract $reacter, ReactionTypeContract $reactionType): bool
    {
        return !$this->isReactedByWithType($reacter, $reactionType);
    }
}
