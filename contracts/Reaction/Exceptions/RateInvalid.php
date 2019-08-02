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

namespace Cog\Contracts\Love\Reaction\Exceptions;

use Cog\Contracts\Love\Exceptions\LoveThrowable;
use UnexpectedValueException;

final class RateInvalid extends UnexpectedValueException implements
    LoveThrowable
{
    public static function withSameValue(float $rate): self
    {
        return new self(sprintf(
            'Invalid Reaction rate: `%s`. Can not change to same value.',
            $rate
        ));
    }
}
