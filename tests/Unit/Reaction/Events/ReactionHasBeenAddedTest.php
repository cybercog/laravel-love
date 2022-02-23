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

namespace Cog\Tests\Laravel\Love\Unit\Reaction\Events;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\Concerns\MocksApplicationServices;

final class ReactionHasBeenAddedTest extends TestCase
{
    use MocksApplicationServices;

    /** @test */
    public function it_fires_reaction_has_been_added_event(): void
    {
        $this->expectsEvents(ReactionHasBeenAdded::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reacter->reactTo($reactant, $reactionType);
    }
}
