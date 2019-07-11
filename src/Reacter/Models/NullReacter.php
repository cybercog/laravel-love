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

use Cog\Contracts\Love\Reactant\Models\Reactant;
use Cog\Contracts\Love\Reacter\Exceptions\ReacterInvalid;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType;
use Illuminate\Support\Collection;
use TypeError;

final class NullReacter implements
    ReacterContract
{
    private $reacterable;

    public function __construct(
        ReacterableContract $reacterable
    ) {
        $this->reacterable = $reacterable;
    }

    public function getId(): string
    {
        throw new TypeError();
    }

    public function getReacterable(): ReacterableContract
    {
        return $this->reacterable;
    }

    public function getReactions(): iterable
    {
        return new Collection();
    }

    public function reactTo(
        Reactant $reactant,
        ReactionType $reactionType
    ): void {
        throw ReacterInvalid::notExists();
    }

    public function unreactTo(
        Reactant $reactant,
        ReactionType $reactionType
    ): void {
        throw ReacterInvalid::notExists();
    }

    public function isReactedTo(
        Reactant $reactant
    ): bool {
        return false;
    }

    public function isNotReactedTo(
        Reactant $reactant
    ): bool {
        return true;
    }

    public function isReactedToWithType(
        Reactant $reactant,
        ReactionType $reactionType
    ): bool {
        return false;
    }

    public function isNotReactedToWithType(
        Reactant $reactant,
        ReactionType $reactionType
    ): bool {
        return true;
    }

    public function isEqualTo(
        ReacterContract $that
    ): bool {
        return $that instanceof self;
    }

    public function isNotEqualTo(
        ReacterContract $that
    ): bool {
        return !$this->isEqualTo($that);
    }

    public function isNull(): bool
    {
        return true;
    }

    public function isNotNull(): bool
    {
        return false;
    }
}
