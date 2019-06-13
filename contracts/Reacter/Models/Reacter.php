<?php

/*
 * This file is part of PHP Contracts: Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Contracts\Love\Reacter\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant;
use Cog\Contracts\Love\Reacterable\Models\Reacterable;
use Cog\Contracts\Love\ReactionType\Models\ReactionType;

interface Reacter
{
    public function getId(): string;

    public function getReacterable(): Reacterable;

    /**
     * @return iterable|\Cog\Contracts\Love\Reaction\Models\Reaction[]
     */
    public function getReactions(): iterable;

    public function reactTo(Reactant $reactant, ReactionType $type): void;

    public function unreactTo(Reactant $reactant, ReactionType $type): void;

    public function isReactedTo(Reactant $reactant): bool;

    public function isNotReactedTo(Reactant $reactant): bool;

    public function isReactedToWithType(Reactant $reactant, ReactionType $type): bool;

    public function isNotReactedToWithType(Reactant $reactant, ReactionType $type): bool;

    public function isEqualTo(self $that): bool;

    public function isNotEqualTo(self $that): bool;

    public function isNull(): bool;

    public function isNotNull(): bool;
}
