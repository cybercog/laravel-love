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

namespace Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions;

use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use OutOfBoundsException;

class ReactionCounterInvalid extends OutOfBoundsException
{
    public static function notFoundOfType(ReactionTypeContract $reactionType): self
    {
        return new static(sprintf(
            'ReactionCounter with ReactionType `%s` not found.',
            $reactionType->getName()
        ));
    }
}
