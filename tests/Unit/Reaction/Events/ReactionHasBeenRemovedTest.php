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
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReactionHasBeenRemovedTest extends TestCase
{
    /** @test */
    public function it_fire_reaction_has_been_removed_event(): void
    {
        Event::fake([ReactionHasBeenRemoved::class]);
        $reactionType = ReactionType::factory()->create();
        $reacter = Reacter::factory()->create();
        $reactant = Reactant::factory()->create();
        $reacter->reactTo($reactant, $reactionType);

        $reacter->unreactTo($reactant, $reactionType);

        Event::assertDispatched(ReactionHasBeenRemoved::class);
    }
}
