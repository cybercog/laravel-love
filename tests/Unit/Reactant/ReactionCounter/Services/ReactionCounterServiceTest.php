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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionCounter\Services;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReactionCounterServiceTest extends TestCase
{
    /** @test */
    public function it_can_add_reaction(): void
    {
        Event::fake(); // To not fire `ReactionObserver` methods
        $reactionType = factory(ReactionType::class)->create([
            'mass' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $service->addReaction($reaction1);
        $service->addReaction($reaction2);

        $counter = $reactant->reactionCounters->first();
        $this->assertSame(2, $counter->count);
        $this->assertSame(8, $counter->weight);
    }

    /** @test */
    public function it_can_remove_reaction(): void
    {
        Event::fake(); // To not fire `ReactionObserver` methods
        $reactionType = factory(ReactionType::class)->create([
            'mass' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
            'count' => 4,
            'weight' => 16,
        ]);
        $service = new ReactionCounterService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $service->removeReaction($reaction1);
        $service->removeReaction($reaction2);

        $counter->refresh();
        $this->assertSame(2, $counter->count);
        $this->assertSame(8, $counter->weight);
    }

    /** @test */
    public function it_not_makes_count_below_zero_on_decrement_count(): void
    {
        Event::fake(); // To not fire `ReactionObserver` methods

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $service = new ReactionCounterService($reactant);
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $service->removeReaction($reaction);

        $this->assertSame(0, $counter->fresh()->getCount());
    }

    /** @test */
    public function it_can_add_reaction_with_negative_weight(): void
    {
        Event::fake(); // To not fire `ReactionObserver` methods
        $reactionType = factory(ReactionType::class)->create([
            'mass' => -4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $service = new ReactionCounterService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $service->addReaction($reaction1);
        $service->addReaction($reaction2);

        $counter->refresh();
        $this->assertSame(2, $counter->count);
        $this->assertSame(-8, $counter->weight);
    }

    /** @test */
    public function it_creates_counter_on_add_reaction_when_counter_not_exists(): void
    {
        Event::fake(); // To not fire `ReactionObserver` methods
        $reactionType = factory(ReactionType::class)->create([
            'mass' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $initialCounters = $reactant->reactionCounters;
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $service = new ReactionCounterService($reactant);

        $service->addReaction($reaction);

        $this->assertCount(0, $initialCounters);
        $this->assertCount(1, $reactant->reactionCounters);
        $this->assertSame(1, $reactant->reactionCounters->get(0)->count);
        $this->assertSame(4, $reactant->reactionCounters->get(0)->weight);
    }

    /** @test */
    public function it_creates_counter_on_remove_reaction_when_counter_not_exists(): void
    {
        Event::fake(); // To not fire `ReactionObserver` methods

        $reactionType = factory(ReactionType::class)->create([
            'mass' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $initialCounters = $reactant->reactionCounters;
        $service = new ReactionCounterService($reactant);
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $service->removeReaction($reaction);

        $this->assertCount(0, $initialCounters);
        $this->assertCount(1, $reactant->reactionCounters);
        $this->assertSame(0, $reactant->reactionCounters->get(0)->count);
        $this->assertSame(0, $reactant->reactionCounters->get(0)->weight);
    }
}
