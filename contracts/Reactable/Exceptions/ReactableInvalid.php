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

namespace Cog\Contracts\Love\Reactable\Exceptions;

use Cog\Contracts\Love\Exceptions\LoveThrowable;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use RuntimeException;

final class ReactableInvalid extends RuntimeException implements
    LoveThrowable
{
    public static function classNotExists(string $type): self
    {
        return new static("[{$type}] class or morph map not found.");
    }

    public static function notImplementInterface(string $type): self
    {
        return new static(sprintf('[%s] must implement `%s` contract.', $type, ReactableContract::class));
    }
}
