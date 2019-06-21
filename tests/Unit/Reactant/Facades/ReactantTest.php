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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\Facades;

use Cog\Laravel\Love\Reactant\Facades\Reactant as ReactantFacade;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReactantTest extends TestCase
{
    /** @test */
    public function it_can_get_reaction_counter_of_type(): void
    {
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        factory(ReactionCounter::class, 2)->create([
            'reactant_id' => $reactant->getId(),
        ]);
        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $assertCounter = $reactantFacade->getReactionCounterOfType($reactionType->getName());

        $this->assertTrue($counter->is($assertCounter));
    }

    /** @test */
    public function it_can_get_null_object_reaction_counter_of_type(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        $reactantFacade = new ReactantFacade($reactant);

        $counter = $reactantFacade->getReactionCounterOfType($reactionType->getName());

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
    }

    /** @test */
    public function it_can_get_null_object_reaction_counter_of_type_with_same_reactant(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        $reactantFacade = new ReactantFacade($reactant);

        $counter = $reactantFacade->getReactionCounterOfType($reactionType->getName());

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactant, $counter->getReactant());
    }

    /** @test */
    public function it_can_get_null_object_reaction_counter_of_type_with_same_reaction_type(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        $reactantFacade = new ReactantFacade($reactant);

        $counter = $reactantFacade->getReactionCounterOfType($reactionType->getName());

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactionType->getId(), $counter->getReactionType()->getId());
    }

    /** @test */
    public function it_can_get_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $total = factory(ReactionTotal::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $assertTotal = $reactantFacade->getReactionTotal();

        $this->assertTrue($total->is($assertTotal));
    }

    /** @test */
    public function it_can_get_null_object_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $reactantFacade = new ReactantFacade($reactant);

        $assertTotal = $reactantFacade->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $assertTotal);
    }

    /** @test */
    public function it_can_get_null_object_reaction_total_with_same_reactant(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $reactantFacade = new ReactantFacade($reactant);

        $assertTotal = $reactantFacade->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $assertTotal);
        $this->assertSame($reactant, $assertTotal->getReactant());
    }
}
