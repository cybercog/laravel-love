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

namespace Cog\Laravel\Love\Reacter\Models;

use Cog\Contracts\Love\Reactant\Exceptions\ReactantInvalid;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reacter\Exceptions\NotAssignedToReacterable;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionAlreadyExists;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionNotExists;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\Support\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Reacter extends Model implements
    ReacterContract
{
    protected $table = 'love_reacters';

    protected $fillable = [
        'type',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function reacterable(): MorphTo
    {
        return $this->morphTo('reacterable', 'type', 'id', 'love_reacter_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reacter_id');
    }

    public function getId(): string
    {
        return $this->getAttributeValue('id');
    }

    public function getReacterable(): ReacterableContract
    {
        $reacterable = $this->getAttribute('reacterable');

        if (is_null($reacterable)) {
            throw new NotAssignedToReacterable();
        }

        return $reacterable;
    }

    public function getReactions(): iterable
    {
        return $this->getAttribute('reactions');
    }

    public function reactTo(
        ReactantContract $reactant,
        ReactionTypeContract $reactionType,
        ?float $rate = null
    ): void {
        if ($reactant->isNull()) {
            throw ReactantInvalid::notExists();
        }

        $reaction = $this->findReaction($reactant, $reactionType);

        if (is_null($reaction)) {
            $this->createReaction($reactant, $reactionType, $rate);

            return;
        }

        if (is_null($rate)) {
            throw new ReactionAlreadyExists(
                sprintf('Reaction of type `%s` already exists.', $reactionType->getName())
            );
        }

        $reaction->changeRate($rate);
    }

    public function unreactTo(
        ReactantContract $reactant,
        ReactionTypeContract $reactionType
    ): void {
        if ($reactant->isNull()) {
            throw ReactantInvalid::notExists();
        }

        $reaction = $this->findReaction($reactant, $reactionType);

        if (is_null($reaction)) {
            throw new ReactionNotExists(
                sprintf('Reaction of type `%s` not exists.', $reactionType->getName())
            );
        }

        $reaction->delete();
    }

    public function hasReactedTo(
        ReactantContract $reactant,
        ?ReactionTypeContract $reactionType = null,
        float $rate = null
    ): bool {
        if ($reactant->isNull()) {
            return false;
        }

        return $reactant->isReactedBy($this, $reactionType, $rate);
    }

    public function hasNotReactedTo(
        ReactantContract $reactant,
        ?ReactionTypeContract $reactionType = null,
        float $rate = null
    ): bool {
        return $reactant->isNotReactedBy($this, $reactionType, $rate);
    }

    public function isEqualTo(
        ReacterContract $that
    ): bool {
        return $that->isNotNull()
            && $this->getId() === $that->getId();
    }

    public function isNotEqualTo(
        ReacterContract $that
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

    private function createReaction(
        ReactantContract $reactant,
        ReactionTypeContract $reactionType,
        ?float $rate
    ): void {
        $this->reactions()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => $rate,
        ]);
    }

    /**
     * @param \Cog\Contracts\Love\Reactant\Models\Reactant $reactant
     * @param \Cog\Contracts\Love\ReactionType\Models\ReactionType $reactionType
     * @return null|\Cog\Contracts\Love\Reaction\Models\Reaction|\Illuminate\Database\Eloquent\Model
     */
    private function findReaction(
        ReactantContract $reactant,
        ReactionTypeContract $reactionType
    ): ?ReactionContract {
        return $this
            ->reactions()
            ->where('reactant_id', $reactant->getId())
            ->where('reaction_type_id', $reactionType->getId())
            ->first();
    }
}
