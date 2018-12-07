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

namespace Cog\Laravel\Love\Reacter\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionAlreadyExists;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionNotExists;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Reacter extends Model implements ReacterContract
{
    protected $table = 'love_reacters';

    protected $fillable = [
        'type',
    ];

    public function reacterable(): MorphTo
    {
        return $this->morphTo('reacterable', 'type', 'id', 'love_reacter_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reacter_id');
    }

    public function getReacterable(): ReacterableContract
    {
        // TODO: Return `NullReacterable` if not set?
        return $this->getAttribute('reacterable');
    }

    public function getReactions(): iterable
    {
        return $this->getAttribute('reactions');
    }

    public function reactTo(ReactantContract $reactant, ReactionTypeContract $reactionType): void
    {
        $attributes = [
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ];

        $reaction = $this->reactions()->where($attributes)->exists();
        if ($reaction) {
            throw new ReactionAlreadyExists(
                sprintf('Reaction of type `%s` already exists.', $reactionType->getName())
            );
        }

        $this->reactions()->create($attributes);
    }

    public function unreactTo(ReactantContract $reactant, ReactionTypeContract $reactionType): void
    {
        $reaction = $this->reactions()->where([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ])->first();

        if (is_null($reaction)) {
            throw new ReactionNotExists(
                sprintf('Reaction of type `%s` not exists.', $reactionType->getName())
            );
        }

        $reaction->delete();
    }

    public function isReactedTo(ReactantContract $reactant): bool
    {
        return $this->reactions()->where([
            'reactant_id' => $reactant->getKey(),
        ])->exists();
    }

    public function isNotReactedTo(ReactantContract $reactant): bool
    {
        return !$this->isReactedTo($reactant);
    }

    public function isReactedWithTypeTo(ReactantContract $reactant, ReactionTypeContract $reactionType): bool
    {
        return $this->reactions()->where([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ])->exists();
    }

    public function isNotReactedWithTypeTo(ReactantContract $reactant, ReactionTypeContract $reactionType): bool
    {
        return !$this->isReactedWithTypeTo($reactant, $reactionType);
    }
}
