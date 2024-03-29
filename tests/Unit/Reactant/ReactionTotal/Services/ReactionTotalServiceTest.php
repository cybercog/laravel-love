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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionTotal\Services;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\Reactant\ReactionTotal\Services\ReactionTotalService;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReactionTotalServiceTest extends TestCase
{
    /** @test */
    public function it_can_add_reaction(): void
    {
        $reactionType = ReactionType::factory()->create([
            'mass' => 4,
        ]);
        $reactant = Reactant::factory()->create();
        $service = new ReactionTotalService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reaction2 = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->addReaction($reaction1);
        $service->addReaction($reaction2);

        $total = $reactant->reactionTotal;
        $this->assertSame(2, $total->count);
        $this->assertSame(8.0, $total->weight);
    }

    /** @test */
    public function it_can_remove_reaction(): void
    {
        $reactionType = ReactionType::factory()->create([
            'mass' => 4,
        ]);
        $reactant = Reactant::factory()->create();
        $total = ReactionTotal::factory()->create([
            'reactant_id' => $reactant->getId(),
            'count' => 4,
            'weight' => 16,
        ]);
        $service = new ReactionTotalService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reaction2 = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->removeReaction($reaction1);
        $service->removeReaction($reaction2);

        $this->assertSame(2, $total->fresh()->count);
        $this->assertSame(8.0, $total->fresh()->weight);
    }

    /** @test */
    public function it_not_makes_count_below_zero_on_decrement_count(): void
    {
        Event::fake(); // To not fire ReactionObserver & Reactant methods

        $reactionType = ReactionType::factory()->create();
        $reactant = Reactant::factory()->create();
        $total = ReactionTotal::factory()->create([
            'reactant_id' => $reactant->getId(),
        ]);
        $service = new ReactionTotalService($reactant);
        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->removeReaction($reaction);

        $this->assertSame(0, $total->fresh()->getCount());
    }

    /** @test */
    public function it_can_add_reaction_with_negative_weight(): void
    {
        $reactionType = ReactionType::factory()->create([
            'mass' => -4,
        ]);
        $reactant = Reactant::factory()->create();
        $service = new ReactionTotalService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reaction2 = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->addReaction($reaction1);
        $service->addReaction($reaction2);

        $total = $reactant->reactionTotal;
        $this->assertSame(2, $total->count);
        $this->assertSame(-8.0, $total->weight);
    }

    /** @test */
    public function it_creates_total_on_add_reaction_when_total_not_exists(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactionType = ReactionType::factory()->create([
            'mass' => 4,
        ]);
        $reactant = Reactant::factory()->create();
        $service = new ReactionTotalService($reactant);
        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->addReaction($reaction);

        $total = $reactant->getReactionTotal();
        $this->assertInstanceOf(ReactionTotal::class, $total);
        $this->assertSame(1, $total->count);
        $this->assertSame(4.0, $total->weight);
    }

    /** @test */
    public function it_creates_total_on_remove_reaction_when_total_not_exists(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactionType = ReactionType::factory()->create();
        $reactant = Reactant::factory()->create();
        $service = new ReactionTotalService($reactant);
        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->removeReaction($reaction);

        $total = $reactant->getReactionTotal();
        $this->assertInstanceOf(ReactionTotal::class, $total);
        $this->assertSame(0, $total->count);
        $this->assertSame(0.0, $total->weight);
    }
}
