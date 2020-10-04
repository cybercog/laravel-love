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

namespace Cog\Contracts\Love\Reacterable\Exceptions;

use Cog\Contracts\Love\Exceptions\LoveThrowable;
use Cog\Contracts\Love\Reacterable\Models\Reacterable;
use RuntimeException;

final class ReacterableInvalid extends RuntimeException implements
    LoveThrowable
{
    public static function classNotExists(string $type): self
    {
        return new self(sprintf(
            '[%s] class or morph map not found.',
            $type
        ));
    }

    public static function notImplementInterface(string $type): self
    {
        return new self(sprintf(
            '[%s] must implement `%s` contract.',
            $type, Reacterable::class
        ));
    }
}
