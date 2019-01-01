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
use DomainException;

final class ReactionTotalInvalid extends DomainException implements
    LoveThrowable
{
    public static function notExists(): self
    {
        return new static('ReactionTotal not exists.');
    }
}
