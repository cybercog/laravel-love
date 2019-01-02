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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionCounter\Services;

use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterBadValue;
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterMissing;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

final class ReactionCounterServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
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
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
            'count' => 4,
            'weight' => 16,
        ]);
        $service = new ReactionCounterService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
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
    public function it_throws_exception_on_decrement_count_below_zero(): void
    {
        $this->expectException(ReactionCounterBadValue::class);

        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $service = new ReactionCounterService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $service->removeReaction($reaction1);
    }

    /** @test */
    public function it_can_add_reaction_with_negative_weight(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => -4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $service = new ReactionCounterService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
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
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $initialCounters = $reactant->reactionCounters;
        // Skip `addReaction` method call from `ReactionObserver`
        Event::fake();
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $service = new ReactionCounterService($reactant);

        $service->addReaction($reaction);

        $this->assertCount(0, $initialCounters);
        $this->assertCount(1, $reactant->reactionCounters);
    }

    /** @test */
    public function it_throws_exception_on_remove_reaction_when_counter_not_exists(): void
    {
        Event::fake(); // Prevent counter auto creation
        $this->expectException(ReactionCounterMissing::class);

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $service->removeReaction($reaction1);
    }
}
