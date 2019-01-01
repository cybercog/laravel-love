<?php

/*
 * This file is part of PHP Contracts: Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions;

use Cog\Contracts\Love\Exceptions\LoveThrowable;
use Cog\Contracts\Love\Reactant\Models\Reactant;
use Cog\Contracts\Love\ReactionType\Models\ReactionType;
use OutOfBoundsException;

final class ReactionCounterMissing extends OutOfBoundsException implements
    LoveThrowable
{
    public static function forReactantOfReactionType(
        Reactant $reactant,
        ReactionType $reactionType
    ): self {
        return new static(sprintf(
            'Reactant with ID `%s` missing ReactionCounter with ReactionType `%s`.',
            $reactant->getId(),
            $reactionType->getName()
        ));
    }
}
