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
use OutOfRangeException;

final class RateOutOfRange extends OutOfRangeException implements
    LoveThrowable
{
    public static function withValueBetween(float $rate, float $minimumRate, float $maximumRate): self
    {
        return new self(sprintf(
            'Invalid Reaction rate: `%s`. Must be between `%s` and `%s`',
            $rate, $minimumRate, $maximumRate
        ));
    }
}
