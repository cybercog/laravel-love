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

namespace Cog\Contracts\Love\Reacterable\Exceptions;

use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use DomainException;

class ReacterableInvalid extends DomainException
{
    public static function notExists(string $type): self
    {
        return new static("[{$type}] class or morph map not found.");
    }

    public static function notImplementInterface(string $type): self
    {
        return new static(sprintf("[%s] must implement `%s` contract.", $type, ReacterableContract::class));
    }
}
