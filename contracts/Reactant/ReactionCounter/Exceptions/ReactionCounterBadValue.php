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

use UnexpectedValueException;

class ReactionCounterBadValue extends UnexpectedValueException
{
    public static function countBelowZero(): self
    {
        return new static('ReactionCounter `count` could not be below zero.');
    }
}
