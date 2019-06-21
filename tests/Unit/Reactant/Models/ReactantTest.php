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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\Models;

use Cog\Contracts\Love\Reactant\Exceptions\NotAssignedToReactable;
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterDuplicate;
use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalDuplicate;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;
use TypeError;

final class ReactantTest extends TestCase
{
    /** @test */
    public function it_can_fill_type(): void
    {
        $reactant = new Reactant([
            'type' => 'TestType',
        ]);

        $this->assertSame('TestType', $reactant->getAttribute('type'));
    }

    /** @test */
    public function it_casts_id_to_string(): void
    {
        $reactant = factory(Reactant::class)->make([
            'id' => 4,
        ]);

        $this->assertSame('4', $reactant->getAttribute('id'));
    }

    /** @test */
    public function it_can_morph_to_reactable(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactant->reactable->is($reactable));
    }

    /** @test */
    public function it_can_has_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReaction = $reactant->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    /** @test */
    public function it_can_has_many_reactions(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReactions = $reactant->reactions;
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_has_reaction_counter(): void
    {
        $reactant = factory(Reactant::class)->create();

        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertCounter = $reactant->reactionCounters->first();
        $this->assertTrue($assertCounter->is($counter));
    }

    /** @test */
    public function it_can_has_many_reaction_counters(): void
    {
        $reactant = factory(Reactant::class)->create();

        $counters = factory(ReactionCounter::class, 2)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertCounters = $reactant->reactionCounters;
        $this->assertTrue($assertCounters->get(0)->is($counters->get(0)));
        $this->assertTrue($assertCounters->get(1)->is($counters->get(1)));
    }

    /** @test */
    public function it_can_has_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();

        $total = factory(ReactionTotal::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactant->reactionTotal->is($total));
    }

    /** @test */
    public function it_can_get_id(): void
    {
        $reactant = factory(Reactant::class)->make([
            'id' => '4',
        ]);

        $this->assertSame('4', $reactant->getId());
    }

    /** @test */
    public function it_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reactant = new Reactant();

        $reactant->getId();
    }

    /** @test */
    public function it_can_get_reactable(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactant->getReactable()->is($reactable));
    }

    /** @test */
    public function it_can_throw_exception_on_get_reacterable_when_not_assigned_to_any_reactable(): void
    {
        $this->expectException(NotAssignedToReactable::class);

        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactant->getReactable();
    }

    /** @test */
    public function it_can_get_reactions(): void
    {
        $reactant = factory(Reactant::class)->create();
        $reactions = factory(Reaction::class, 2)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReactions = $reactant->getReactions();

        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_get_reaction_counters(): void
    {
        $reactant = factory(Reactant::class)->create();
        $counters = factory(ReactionCounter::class, 2)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertCounters = $reactant->getReactionCounters();

        $this->assertTrue($assertCounters->get(0)->is($counters->get(0)));
        $this->assertTrue($assertCounters->get(1)->is($counters->get(1)));
    }

    /** @test */
    public function it_can_get_reaction_counter_of_type(): void
    {
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);
        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertCounter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertTrue($assertCounter->is($counter));
    }

    /** @test */
    public function it_can_get_null_object_reaction_counter_of_type(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
    }

    /** @test */
    public function it_can_get_null_object_reaction_counter_of_type_with_same_reactant(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactant, $counter->getReactant());
    }

    /** @test */
    public function it_can_get_null_object_reaction_counter_of_type_with_same_reaction_type(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactionType, $counter->getReactionType());
    }

    /** @test */
    public function it_can_get_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();
        $total = factory(ReactionTotal::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertTotal = $reactant->getReactionTotal();

        $this->assertTrue($total->is($assertTotal));
    }

    /** @test */
    public function it_can_get_null_object_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();

        $assertTotal = $reactant->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $assertTotal);
    }

    /** @test */
    public function it_can_get_null_object_reaction_total_with_same_reactant(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = factory(Reactant::class)->create();

        $assertTotal = $reactant->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $assertTotal);
        $this->assertSame($reactant, $assertTotal->getReactant());
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = $reactant->isReactedBy($reacter);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacter_when_reacter_is_null_object(): void
    {
        $reacter = new NullReacter(new Bot());
        $reactant = factory(Reactant::class)->create();

        $isReacted = $reactant->isReactedBy($reacter);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacter_when_reacter_is_not_persisted(): void
    {
        $reacter = new Reacter();
        $reactant = factory(Reactant::class)->create();

        $isReacted = $reactant->isReactedBy($reacter);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isNotReacted = $reactant->isNotReactedBy($reacter);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacter_when_reacter_is_null_object(): void
    {
        $reacter = new NullReacter(new Bot());
        $reactant = factory(Reactant::class)->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacter_when_reacter_is_not_persisted(): void
    {
        $reacter = new Reacter();
        $reactant = factory(Reactant::class)->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacter_with_type(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = $reactant->isReactedByWithType($reacter, $reactionType);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacter_with_type_when_reacter_is_null_object(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = new NullReacter(new Bot());
        $reactant = factory(Reactant::class)->create();

        $isReacted = $reactant->isReactedByWithType($reacter, $reactionType);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_reacter_with_type_when_reacter_is_not_persisted(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = new Reacter();
        $reactant = factory(Reactant::class)->create();

        $isReacted = $reactant->isReactedByWithType($reacter, $reactionType);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacter_with_type(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $otherReactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isNotReacted = $reactant->isNotReactedByWithType($reacter, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacter_with_type_when_reacter_is_null_object(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = new NullReacter(new Bot());
        $reactant = factory(Reactant::class)->create();

        $isNotReacted = $reactant->isNotReactedByWithType($reacter, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_reacter_with_type_when_reacter_is_not_persisted(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = new Reacter();
        $reactant = factory(Reactant::class)->create();

        $isNotReacted = $reactant->isNotReactedByWithType($reacter, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_equal_to_self(): void
    {
        $reactant = factory(Reactant::class)->create();

        $this->assertTrue($reactant->isEqualTo($reactant));
    }

    /** @test */
    public function it_can_check_is_equal_to_other_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();
        $otherReactant = factory(Reactant::class)->create();

        $this->assertFalse($reactant->isEqualTo($otherReactant));
    }

    /** @test */
    public function it_can_check_is_equal_to_null_object_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();
        $nullReactant = new NullReactant(new Article());

        $this->assertFalse($reactant->isEqualTo($nullReactant));
    }

    /** @test */
    public function it_can_check_is_equal_to_null_object_reactant_when_not_null_object_not_persisted(): void
    {
        $reactant = new Reactant();
        $nullReactant = new NullReactant(new Article());

        $this->assertFalse($reactant->isEqualTo($nullReactant));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_self(): void
    {
        $reactant = factory(Reactant::class)->create();

        $this->assertFalse($reactant->isNotEqualTo($reactant));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_other_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();
        $otherReactant = factory(Reactant::class)->create();

        $this->assertTrue($reactant->isNotEqualTo($otherReactant));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_null_object_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();
        $nullReactant = new NullReactant(new Article());

        $this->assertTrue($reactant->isNotEqualTo($nullReactant));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_null_object_reactant_when_not_null_object_not_persisted(): void
    {
        $reactant = new Reactant();
        $nullReactant = new NullReactant(new Article());

        $this->assertTrue($reactant->isNotEqualTo($nullReactant));
    }

    /** @test */
    public function it_can_create_reaction_counter_of_type(): void
    {
        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $reactant->createReactionCounterOfType($reactionType);

        $counters = $reactant->getReactionCounters();
        $this->assertCount(1, $counters);
        /** @var \Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter $counter */
        $counter = $counters[0];
        $this->assertTrue($counter->isReactionOfType($reactionType));
        $this->assertTrue($counter->getReactant()->is($reactant));
        $this->assertSame(0, $counter->getCount());
        $this->assertSame(0, $counter->getWeight());
    }

    /** @test */
    public function it_throw_exception_on_can_create_reaction_counter_of_type_when_counter_of_same_type_already_exists(): void
    {
        $this->expectException(ReactionCounterDuplicate::class);

        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $reactant->createReactionCounterOfType($reactionType);
        $reactant->createReactionCounterOfType($reactionType);
    }

    /** @test */
    public function it_can_create_reaction_total(): void
    {
        /** @var \Cog\Contracts\Love\Reactant\Models\Reactant $reactant */
        $reactant = factory(Reactant::class)->create();

        $reactant->createReactionTotal();

        $total = $reactant->getReactionTotal();
        $this->assertTrue($total->getReactant()->is($reactant));
        $this->assertSame(0, $total->getCount());
        $this->assertSame(0, $total->getWeight());
    }

    /** @test */
    public function it_throw_exception_on_can_create_reaction_total_when_total_already_exists(): void
    {
        $this->expectException(ReactionTotalDuplicate::class);

        $reactant = factory(Reactant::class)->create();

        $reactant->createReactionTotal();
        $reactant->createReactionTotal();
    }

    /** @test */
    public function it_can_check_is_null(): void
    {
        $reactant = factory(Reactant::class)->create();

        $this->assertFalse($reactant->isNull());
    }

    /** @test */
    public function it_can_check_is_null_when_reactant_not_persisted(): void
    {
        $reactant = new Reactant();

        $this->assertTrue($reactant->isNull());
    }

    /** @test */
    public function it_can_check_is_not_null(): void
    {
        $reactant = factory(Reactant::class)->create();

        $this->assertTrue($reactant->isNotNull());
    }

    /** @test */
    public function it_can_check_is_not_null_when_reactant_not_persisted(): void
    {
        $reactant = new Reactant();

        $this->assertFalse($reactant->isNotNull());
    }
}
