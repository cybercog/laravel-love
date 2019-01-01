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

namespace Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions;

use Cog\Contracts\Love\Exceptions\LoveThrowable;
use UnexpectedValueException;

final class ReactionTotalBadValue extends UnexpectedValueException implements
    LoveThrowable
{
    public static function totalCountBelowZero(): self
    {
        return new static('ReactionTotal `total_count` could not be below zero.');
    }
}
