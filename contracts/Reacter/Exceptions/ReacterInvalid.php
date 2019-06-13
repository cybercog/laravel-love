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

namespace Cog\Contracts\Love\Reacter\Exceptions;

use Cog\Contracts\Love\Exceptions\LoveThrowable;
use RuntimeException;

final class ReacterInvalid extends RuntimeException implements
    LoveThrowable
{
    public static function notExists(): self
    {
        return new static('Reacter not exists.');
    }
}
