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
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\Stubs\Models\UserWithoutAutoReacterCreate;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReactantTest extends TestCase
{
    /** @test */
    public function it_can_get_reactions(): void
    {
        $reactant = Reactant::factory()->create();
        $reactions = Reaction::factory()->count(2)->create([
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $assertReactions = $reactantFacade->getReactions();

        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_get_reactions_by_user(): void
    {
        $reactant = Reactant::factory()->create();
        $reacterable = User::factory()->create();
        $reactions = Reaction::factory()->count(2)->create([
            'reactant_id' => $reactant->getId(),
            'reacter_id' => $reacterable->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->count(3)->create([
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $assertReactions = $reactantFacade->getReactionsBy($reacterable);

        $this->assertCount(2, $assertReactions);
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_get_reaction_counters(): void
    {
        $reactant = Reactant::factory()->create();
        $counters = ReactionCounter::factory()->count(2)->create([
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $assertCounters = $reactantFacade->getReactionCounters();

        $this->assertTrue($assertCounters->get(0)->is($counters->get(0)));
        $this->assertTrue($assertCounters->get(1)->is($counters->get(1)));
    }

    /** @test */
    public function it_can_get_reaction_counter_of_type(): void
    {
        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();
        ReactionCounter::factory()->create([
            'reactant_id' => $reactant->getId(),
        ]);
        $counter = ReactionCounter::factory()->create([
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
        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $counter = $reactantFacade->getReactionCounterOfType($reactionType->getName());

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
    }

    /** @test */
    public function it_can_get_null_object_reaction_counter_of_type_with_same_reactant(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $counter = $reactantFacade->getReactionCounterOfType($reactionType->getName());

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactant, $counter->getReactant());
    }

    /** @test */
    public function it_can_get_null_object_reaction_counter_of_type_with_same_reaction_type(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $counter = $reactantFacade->getReactionCounterOfType($reactionType->getName());

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactionType->getId(), $counter->getReactionType()->getId());
    }

    /** @test */
    public function it_can_get_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();
        $total = ReactionTotal::factory()->create([
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
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $assertTotal = $reactantFacade->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $assertTotal);
    }

    /** @test */
    public function it_can_get_null_object_reaction_total_with_same_reactant(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $assertTotal = $reactantFacade->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $assertTotal);
        $this->assertSame($reactant, $assertTotal->getReactant());
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = User::factory()->create();
        $reacter = $reacterable->getLoveReacter();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_when_reacter_is_null(): void
    {
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy(null);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_when_reacter_is_null_object(): void
    {
        $reacterable = UserWithoutAutoReacterCreate::factory()->create();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_when_reacter_is_not_persisted(): void
    {
        $reacterable = new User();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = User::factory()->create();
        $reacter = $reacterable->getLoveReacter();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_when_reacter_is_null(): void
    {
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy(null);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_when_reacter_is_null_object(): void
    {
        $reacterable = UserWithoutAutoReacterCreate::factory()->create();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_when_reacter_is_not_persisted(): void
    {
        $reacterable = new User();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_type(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = User::factory()->create();
        $reacter = $reacterable->getLoveReacter();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, $reactionType->getName());

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_type_when_reacter_is_null_object(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = UserWithoutAutoReacterCreate::factory()->create();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, $reactionType->getName());

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_type_when_reacter_is_not_persisted(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = new User();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, $reactionType->getName());

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_type(): void
    {
        $reactionType = ReactionType::factory()->create();
        $otherReactionType = ReactionType::factory()->create();
        $reacterable = User::factory()->create();
        $reacter = $reacterable->getLoveReacter();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $otherReactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, $reactionType->getName());

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_type_when_reacter_is_null_object(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = UserWithoutAutoReacterCreate::factory()->create();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, $reactionType->getName());

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_type_when_reacter_is_not_persisted(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = new User();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, $reactionType->getName());

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_rate(): void
    {
        $reacterable = User::factory()->create();
        $reacter = $reacterable->getLoveReacter();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => 2.0,
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, null, 2.0);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_rate_when_reacter_is_null_object(): void
    {
        $reacterable = UserWithoutAutoReacterCreate::factory()->create();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, null, 2.0);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_rate_when_reacter_is_not_persisted(): void
    {
        $reacterable = new User();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, null, 2.0);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_rate(): void
    {
        $reacterable = User::factory()->create();
        $reacter = $reacterable->getLoveReacter();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => 2.2,
        ]);
        Reaction::factory()->create([
            'reactant_id' => $reactant->getId(),
            'rate' => 2.0,
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, null, 2.0);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_rate_when_reacter_is_null_object(): void
    {
        $reacterable = UserWithoutAutoReacterCreate::factory()->create();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, null, 2.0);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_rate_when_reacter_is_not_persisted(): void
    {
        $reacterable = new User();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, null, 2.0);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_type_and_rate(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = User::factory()->create();
        $reacter = $reacterable->getLoveReacter();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => 2.0,
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, $reactionType->getName(), 2.0);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_type_and_rate_when_reacter_is_null_object(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = UserWithoutAutoReacterCreate::factory()->create();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, $reactionType->getName(), 2.0);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacterable_with_type_and_rate_when_reacter_is_not_persisted(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = new User();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isReacted = $reactantFacade->isReactedBy($reacterable, $reactionType->getName(), 2.0);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_type_and_rate(): void
    {
        $reactionType = ReactionType::factory()->create();
        $otherReactionType = ReactionType::factory()->create();
        $reacterable = User::factory()->create();
        $reacter = $reacterable->getLoveReacter();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $otherReactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => 2.2,
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => 2.0,
        ]);
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, $reactionType->getName(), 2.0);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_type_and_rate_when_reacter_is_null_object(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = UserWithoutAutoReacterCreate::factory()->create();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, $reactionType->getName(), 2.0);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacterable_with_type_and_rate_when_reacter_is_not_persisted(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacterable = new User();
        $reactant = Reactant::factory()->create();
        $reactantFacade = new ReactantFacade($reactant);

        $isNotReacted = $reactantFacade->isNotReactedBy($reacterable, $reactionType->getName(), 2.0);

        $this->assertTrue($isNotReacted);
    }
}
