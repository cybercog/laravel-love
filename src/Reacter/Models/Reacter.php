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

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reacter extends Model
{
    protected $table = 'love_reacters';

    public function reacterable(): MorphTo
    {
        return $this->morphTo('reacterable', 'type', 'id', 'love_reacter_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reacter_id');
    }

    public function reactTo(Reactant $reactant, ReactionType $reactionType): void
    {
        $attributes = [
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ];

        $reaction = $this->reactions()->where($attributes)->exists();
        if ($reaction) {
            // TODO: Throw custom typed exception
            throw new \RuntimeException(
                sprintf('Reaction of type `%s` already exists.', $reactionType->getAttribute('name'))
            );
        }

        $this->reactions()->create($attributes);
    }

    public function unreactTo(Reactant $reactant, ReactionType $reactionType): void
    {
        $reaction = $this->reactions()
            ->where('reaction_type_id', $reactionType->getKey())
            ->where('reactant_id', $reactant->getKey())
            ->first();

        if (is_null($reaction)) {
            // TODO: Throw custom typed exception
            throw new \RuntimeException(
                sprintf('Reaction of type `%s` not exists.', $reactionType->getAttribute('name'))
            );
        }

        $reaction->delete();
    }

    // TODO: Add ReactionType
    public function isReactedTo(Reactant $reactant): bool
    {
        return $this->reactions()->where([
            'reactant_id' => $reactant->getKey(),
        ])->exists();
    }

    // TODO: Add ReactionType
    public function isNotReactedTo(Reactant $reactant): bool
    {
        return !$this->isReactedTo($reactant);
    }
}
