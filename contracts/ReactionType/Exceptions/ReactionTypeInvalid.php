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

namespace Cog\Contracts\Love\ReactionType\Exceptions;

use Cog\Contracts\Love\Exceptions\LoveThrowable;
use RuntimeException;

final class ReactionTypeInvalid extends RuntimeException implements
    LoveThrowable
{
    public static function nameNotExists(string $name): self
    {
        return new self(sprintf(
            'ReactionType with name `%s` not exists.',
            $name
        ));
    }
}
