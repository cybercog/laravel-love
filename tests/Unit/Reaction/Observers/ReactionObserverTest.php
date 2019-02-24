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

namespace Cog\Tests\Laravel\Love\Unit\Reaction\Observers;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class ReactionObserverTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_counter_on_reaction_created_when_counter_not_exists(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $initialCounters = $reactant->reactionCounters;

        factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $assertCounters = $reactant->fresh()->reactionCounters;
        $this->assertCount(0, $initialCounters);
        $this->assertCount(1, $assertCounters);
        $this->assertSame(1, $assertCounters->get(0)->count);
        $this->assertSame(4, $assertCounters->get(0)->weight);
    }

    /** @test */
    public function it_not_creates_counter_on_reaction_created_when_counter_already_exists(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $assertCounters = $reactant->fresh()->reactionCounters;
        $this->assertCount(1, $assertCounters);
        $this->assertTrue($assertCounters->get(0)->is($counter));
    }

    /** @test */
    public function it_increment_reactions_count_on_reaction_created(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertSame(1, $counter->fresh()->count);
    }

    /** @test */
    public function it_not_creates_counter_on_reaction_deleted(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        ReactionCounter::query()->truncate();

        $reaction->fresh()->delete();

        $assertCounters = $reactant->fresh()->reactionCounters;
        $this->assertCount(1, $assertCounters);
        $this->assertSame(0, $assertCounters->get(0)->count);
        $this->assertSame(0, $assertCounters->get(0)->weight);
    }

    /** @test */
    public function it_decrement_reactions_count_on_reaction_deleted(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $reactions = factory(Reaction::class, 2)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $reactions->get(0)->delete();

        $this->assertSame(1, $counter->fresh()->count);
    }

    /** @test */
    public function it_increment_reactions_weight_on_reaction_created(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        factory(Reaction::class, 2)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertSame(8, $counter->fresh()->weight);
    }

    /** @test */
    public function it_decrement_reactions_weight_on_reaction_deleted(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $reactions = factory(Reaction::class, 3)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $reactions->get(0)->delete();

        $this->assertSame(8, $counter->fresh()->weight);
    }

    /** @test */
    public function it_increment_reactions_weight_on_reaction_with_negative_weight_created(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => -4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        factory(Reaction::class, 2)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertSame(-8, $counter->fresh()->weight);
    }

    /** @test */
    public function it_decrement_reactions_weight_on_reaction_with_negative_weight_deleted(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => -4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $reactions = factory(Reaction::class, 3)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $reactions->get(0)->delete();

        $this->assertSame(-8, $counter->fresh()->weight);
    }

    /** @test */
    public function it_increment_reactions_total_count_on_reaction_created(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $total = $reactant->reactionTotal;
        $this->assertSame(1, $total->count);
    }

    /** @test */
    public function it_decrement_reactions_total_count_on_reaction_deleted(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $reactions = factory(Reaction::class, 2)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $reactions->get(0)->delete();

        $total = $reactant->reactionTotal;
        $this->assertSame(1, $total->count);
    }

    /** @test */
    public function it_increment_reactions_total_weight_on_reaction_created(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();

        factory(Reaction::class, 2)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $total = $reactant->reactionTotal;
        $this->assertSame(8, $total->weight);
    }

    /** @test */
    public function it_decrement_reactions_total_weight_on_reaction_deleted(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $reactions = factory(Reaction::class, 3)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $reactions->get(0)->delete();

        $total = $reactant->reactionTotal;
        $this->assertSame(8, $total->weight);
    }

    /** @test */
    public function it_increment_reactions_total_weight_on_reaction_with_negative_weight_created(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => -4,
        ]);
        $reactant = factory(Reactant::class)->create();

        factory(Reaction::class, 2)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $total = $reactant->reactionTotal;
        $this->assertSame(-8, $total->weight);
    }

    /** @test */
    public function it_decrement_reactions_total_weight_on_reaction_with_negative_weight_deleted(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => -4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $reactions = factory(Reaction::class, 3)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $reactions->get(0)->delete();

        $total = $reactant->reactionTotal;
        $this->assertSame(-8, $total->weight);
    }
}
