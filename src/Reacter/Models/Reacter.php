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
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reacter\Exceptions\NotAssignedToReacterable;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterInterface;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionAlreadyExists;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionNotExists;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\Support\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Reacter extends Model implements
    ReacterInterface
{
    protected $table = 'love_reacters';

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

    public function getReacterable(): ReacterableInterface
    {
        $reacterable = $this->getAttribute('reacterable');

        if ($reacterable === null) {
            throw new NotAssignedToReacterable();
        }

        return $reacterable;
    }

    public function getReactions(): iterable
    {
        return $this->getAttribute('reactions');
    }

    public function reactTo(
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType,
        ?float $rate = null
    ): void {
        if ($reactant->isNull()) {
            throw ReactantInvalid::notExists();
        }

        $reaction = $this->findReaction($reactant, $reactionType);

        if ($reaction === null) {
            $this->createReaction($reactant, $reactionType, $rate);

            return;
        }

        if ($rate === null) {
            throw ReactionAlreadyExists::ofType($reactionType);
        }

        $reaction->changeRate($rate);
    }

    public function unreactTo(
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType
    ): void {
        if ($reactant->isNull()) {
            throw ReactantInvalid::notExists();
        }

        $reaction = $this->findReaction($reactant, $reactionType);

        if ($reaction === null) {
            throw new ReactionNotExists(sprintf(
                'Reaction of type `%s` not exists.',
                $reactionType->getName()
            ));
        }

        $reaction->delete();
    }

    public function hasReactedTo(
        ReactantInterface $reactant,
        ?ReactionTypeInterface $reactionType = null,
        ?float $rate = null
    ): bool {
        if ($reactant->isNull()) {
            return false;
        }

        return $reactant->isReactedBy($this, $reactionType, $rate);
    }

    public function hasNotReactedTo(
        ReactantInterface $reactant,
        ?ReactionTypeInterface $reactionType = null,
        ?float $rate = null
    ): bool {
        return $reactant->isNotReactedBy($this, $reactionType, $rate);
    }

    public function isEqualTo(
        ReacterInterface $that
    ): bool {
        return $that->isNotNull()
            && $this->getId() === $that->getId();
    }

    public function isNotEqualTo(
        ReacterInterface $that
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
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType,
        ?float $rate = null
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
     * @return \Cog\Contracts\Love\Reaction\Models\Reaction|\Illuminate\Database\Eloquent\Model|null
     */
    private function findReaction(
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType
    ): ?ReactionInterface {
        return $this
            ->reactions()
            ->where('reactant_id', $reactant->getId())
            ->where('reaction_type_id', $reactionType->getId())
            ->first();
    }
}
