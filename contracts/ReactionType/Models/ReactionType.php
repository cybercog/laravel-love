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

namespace Cog\Contracts\Love\ReactionType\Models;

interface ReactionType
{
    public static function fromName(string $name): self;

    public function getId(): string;

    public function getName(): string;

    public function getMass(): int;

    public function isEqualTo(self $that): bool;

    public function isNotEqualTo(self $that): bool;
}
