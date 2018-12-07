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

namespace Cog\Laravel\Love\Reactant\Models;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionSummary\Models\ReactionSummary as ReactionSummaryContract;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\NullReactionSummary;

final class NullReactant implements ReactantContract
{
    private $reactable;

    public function __construct(ReactableContract $reactable)
    {
        $this->reactable = $reactable;
    }

    public function getReactable(): ReactableContract
    {
        // TODO: Return `NullReactable` if not set?
        return $this->reactable;
    }

    public function getReactions(): iterable
    {
        return [];
    }

    public function getReactionCounters(): iterable
    {
        return [];
    }

    public function getReactionSummary(): ReactionSummaryContract
    {
        return new NullReactionSummary($this);
    }
}
