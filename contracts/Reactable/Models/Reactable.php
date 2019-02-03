<?php

/*
 * This file is part of PHP Contracts: Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Contracts\Love\Reactable\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant;

interface Reactable
{
    public function getLoveReactant(): Reactant;

    public function isRegisteredAsLoveReactant(): bool;

    public function isNotRegisteredAsLoveReactant(): bool;

    /**
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\AlreadyRegisteredAsLoveReactant
     */
    public function registerAsLoveReactant(): void;
}
