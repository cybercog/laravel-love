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

namespace Cog\Contracts\Love\Liker\Exceptions;

use RuntimeException;

/**
 * Class InvalidLiker.
 *
 * @package Cog\Contracts\Love\Liker\Exceptions
 */
class InvalidLiker extends RuntimeException
{
    public static function notDefined()
    {
        return new static('Liker not defined.');
    }
}
