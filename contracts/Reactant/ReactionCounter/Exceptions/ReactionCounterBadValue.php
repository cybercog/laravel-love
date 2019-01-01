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
use UnexpectedValueException;

final class ReactionCounterBadValue extends UnexpectedValueException implements
    LoveThrowable
{
    public static function countBelowZero(): self
    {
        return new static('ReactionCounter `count` could not be below zero.');
    }
}
