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

namespace Cog\Contracts\Love\Like\Exceptions;

use RuntimeException;

/**
 * Class InvalidLikeType.
 *
 * @package Cog\Contracts\Love\Like\Exceptions
 */
class InvalidLikeType extends RuntimeException
{
    public static function notExists(string $type)
    {
        return new static("Like type `{$type}` not exist.");
    }
}
