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

namespace Cog\Contracts\Love\ReactionType\Exceptions;

use RuntimeException;

class ReactionTypeInvalid extends RuntimeException
{
    public static function notExists(string $name): self
    {
        return new static("ReactionType with name `{$name}` not found.");
    }
}
