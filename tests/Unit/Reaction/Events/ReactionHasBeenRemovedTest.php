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

namespace Cog\Laravel\Love\Tests\Unit\Reaction\Events;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;

final class ReactionHasBeenRemovedTest extends TestCase
{
    /** @test */
    public function it_fire_reaction_has_been_removed_event(): void
    {
        $this->expectsEvents(ReactionHasBeenRemoved::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reacter->reactTo($reactant, $reactionType);

        $reacter->unreactTo($reactant, $reactionType);
    }
}
