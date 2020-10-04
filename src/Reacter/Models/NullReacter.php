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

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reacter\Exceptions\ReacterInvalid;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterInterface;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Illuminate\Support\Collection;
use TypeError;

final class NullReacter implements
    ReacterInterface
{
    /**
     * @var ReacterableInterface
     */
    private $reacterable;

    public function __construct(
        ReacterableInterface $reacterable
    ) {
        $this->reacterable = $reacterable;
    }

    public function getId(): string
    {
        throw new TypeError();
    }

    public function getReacterable(): ReacterableInterface
    {
        return $this->reacterable;
    }

    public function getReactions(): iterable
    {
        return new Collection();
    }

    public function reactTo(
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType,
        ?float $rate = null
    ): void {
        throw ReacterInvalid::notExists();
    }

    public function unreactTo(
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType
    ): void {
        throw ReacterInvalid::notExists();
    }

    public function hasReactedTo(
        ReactantInterface $reactant,
        ?ReactionTypeInterface $reactionType = null,
        ?float $rate = null
    ): bool {
        return false;
    }

    public function hasNotReactedTo(
        ReactantInterface $reactant,
        ?ReactionTypeInterface $reactionType = null,
        ?float $rate = null
    ): bool {
        return true;
    }

    public function isEqualTo(
        ReacterInterface $that
    ): bool {
        return $that instanceof self;
    }

    public function isNotEqualTo(
        ReacterInterface $that
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
