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

namespace Cog\Contracts\Love\Reacterable\Models;

use Cog\Contracts\Love\Reacter\Models\Reacter;

interface Reacterable
{
    public function getLoveReacter(): Reacter;

    public function isRegisteredAsLoveReacter(): bool;

    public function isNotRegisteredAsLoveReacter(): bool;

    public function registerAsLoveReacter(): void;
}
