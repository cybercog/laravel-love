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

namespace Cog\Contracts\Love\ReactionType\Models;

interface ReactionType
{
    public static function fromName(string $name): self;

    public function getName(): string;

    public function getWeight(): int;
}
