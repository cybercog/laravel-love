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
use Cog\Contracts\Love\ReactionType\Models\ReactionType;
use RuntimeException;

final class ReactionAlreadyExists extends RuntimeException implements
    LoveThrowable
{
    public static function ofType(ReactionType $reactionType): self
    {
        return new self(sprintf(
            'Reaction of type `%s` already exists.',
            $reactionType->getName()
        ));
    }
}
