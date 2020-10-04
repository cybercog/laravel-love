<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love\Reactant\Jobs;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionInterface;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotal\Services\ReactionTotalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue as ShouldQueueInterface;
use Illuminate\Foundation\Bus\Dispatchable;

final class IncrementReactionAggregatesJob implements
    ShouldQueueInterface
{
    use Dispatchable;
    use Queueable;

    /**
     * @var \Cog\Contracts\Love\Reactant\Models\Reactant
     */
    private $reactant;

    /**
     * @var \Cog\Contracts\Love\Reaction\Models\Reaction
     */
    private $reaction;

    /**
     * @param \Cog\Contracts\Love\Reactant\Models\Reactant $reactant
     * @param \Cog\Contracts\Love\Reaction\Models\Reaction $reaction
     */
    public function __construct(
        ReactantInterface $reactant,
        ReactionInterface $reaction
    ) {
        $this->reactant = $reactant;
        $this->reaction = $reaction;
    }

    public function handle(): void
    {
        (new ReactionCounterService($this->reactant))
            ->addReaction($this->reaction);

        (new ReactionTotalService($this->reactant))
            ->addReaction($this->reaction);
    }

    /**
     * @return ReactantInterface
     */
    public function getReactant(): ReactantInterface
    {
        return $this->reactant;
    }

    /**
     * @return ReactionInterface
     */
    public function getReaction(): ReactionInterface
    {
        return $this->reaction;
    }
}
