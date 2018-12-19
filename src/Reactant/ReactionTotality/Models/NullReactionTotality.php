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

namespace Cog\Laravel\Love\Reactant\ReactionTotality\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionTotality\Models\ReactionTotality as ReactionTotalityContract;

final class NullReactionTotality implements ReactionTotalityContract
{
    private $reactant;

    public function __construct(ReactantContract $reactant)
    {
        $this->reactant = $reactant;
    }

    public function getReactant(): ReactantContract
    {
        return $this->reactant;
    }

    public function getCount(): int
    {
        return 0;
    }

    public function getWeight(): int
    {
        return 0;
    }
}
