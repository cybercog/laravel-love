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

namespace Cog\Contracts\Love\Reacter\Facades;

use Cog\Contracts\Love\Reactable\Models\Reactable;

interface Reacter
{
    public function getReactions(): iterable;

    public function reactTo(
        Reactable $reactable,
        string $reactionTypeName,
        float | null $rate = null,
    ): void;

    public function unreactTo(
        Reactable $reactable,
        string $reactionTypeName,
    ): void;

    public function hasReactedTo(
        Reactable $reactable,
        string | null $reactionTypeName = null,
        float | null $rate = null,
    ): bool;

    public function hasNotReactedTo(
        Reactable $reactable,
        string | null $reactionTypeName = null,
        float | null $rate = null,
    ): bool;
}
