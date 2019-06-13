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

namespace Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions;

use Cog\Contracts\Love\Exceptions\LoveThrowable;
use Cog\Contracts\Love\Reactant\Models\Reactant;
use Cog\Contracts\Love\ReactionType\Models\ReactionType;
use RuntimeException;

final class ReactionCounterDuplicate extends RuntimeException implements
    LoveThrowable
{
    public static function ofTypeForReactant(
        ReactionType $reactionType,
        Reactant $reactant
    ): self {
        return new static(sprintf(
            'ReactionCounter for Reactant `%s` with ReactionType `%s` already exists.',
            $reactant->getId(),
            $reactionType->getId()
        ));
    }
}
