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
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;
use TypeError;

final class ReactantTest extends TestCase
{
    public function test_can_fill_type(): void
    {
        $reactant = new Reactant([
            'type' => 'TestType',
        ]);

        $this->assertSame('TestType', $reactant->getAttribute('type'));
    }

    public function test_casts_id_to_string(): void
    {
        $reactant = Reactant::factory()->make([
            'id' => 4,
        ]);

        $this->assertSame('4', $reactant->getAttribute('id'));
        $this->assertSame('4', $reactant->getId());
    }

    public function test_can_morph_to_reactable(): void
    {
        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = Article::factory()->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactant->reactable->is($reactable));
    }

    public function test_can_has_reaction(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reactant = Reactant::factory()->create();

        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReaction = $reactant->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    public function test_can_has_many_reactions(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reactant = Reactant::factory()->create();

        $reactions = Reaction::factory()->count(2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReactions = $reactant->reactions;
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    public function test_can_has_reaction_counter(): void
    {
        $reactant = Reactant::factory()->create();

        $counter = ReactionCounter::factory()->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertCounter = $reactant->reactionCounters->first();
        $this->assertTrue($assertCounter->is($counter));
    }

    public function test_can_has_many_reaction_counters(): void
    {
        $reactant = Reactant::factory()->create();

        $counters = ReactionCounter::factory()->count(2)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertCounters = $reactant->reactionCounters;
        $this->assertTrue($assertCounters->get(0)->is($counters->get(0)));
        $this->assertTrue($assertCounters->get(1)->is($counters->get(1)));
    }

    public function test_can_has_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();

        $total = ReactionTotal::factory()->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactant->reactionTotal->is($total));
    }

    public function test_can_get_id(): void
    {
        $reactant = Reactant::factory()->make([
            'id' => '4',
        ]);

        $this->assertSame('4', $reactant->getId());
    }

    public function test_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reactant = new Reactant();

        $reactant->getId();
    }

    public function test_can_get_reactable(): void
    {
        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = Article::factory()->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactant->getReactable()->is($reactable));
    }

    public function test_can_throw_exception_on_get_reacterable_when_not_assigned_to_any_reactable(): void
    {
        $this->expectException(NotAssignedToReactable::class);

        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactant->getReactable();
    }

    public function test_can_get_reactions(): void
    {
        $reactant = Reactant::factory()->create();
        $reactions = Reaction::factory()->count(2)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReactions = $reactant->getReactions();

        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    public function test_can_get_reactions_by_reacter(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reactant = Reactant::factory()->create();
        $reacter = Reacter::factory()->create();

        $reactions = Reaction::factory()->count(2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
            'reacter_id' => $reacter->getId(),
        ]);
        Reaction::factory()->count(3)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReactions = $reactant->getReactionsBy($reacter);
        $this->assertCount(2, $assertReactions);
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    public function test_can_get_reactions_by_null_reacter(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reactant = Reactant::factory()->create();
        $nullReacter = new NullReacter(new User());

        Reaction::factory()->count(3)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReactions = $reactant->getReactionsBy($nullReacter);
        $this->assertCount(0, $assertReactions);
    }

    public function test_can_get_reaction_counters(): void
    {
        $reactant = Reactant::factory()->create();
        $counters = ReactionCounter::factory()->count(2)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertCounters = $reactant->getReactionCounters();

        $this->assertTrue($assertCounters->get(0)->is($counters->get(0)));
        $this->assertTrue($assertCounters->get(1)->is($counters->get(1)));
    }

    public function test_can_get_reaction_counter_of_type(): void
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

        $assertCounter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertTrue($assertCounter->is($counter));
    }

    public function test_can_get_null_object_reaction_counter_of_type(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
    }

    public function test_can_get_null_object_reaction_counter_of_type_with_same_reactant(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactant, $counter->getReactant());
    }

    public function test_can_get_null_object_reaction_counter_of_type_with_same_reaction_type(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactionType, $counter->getReactionType());
    }

    public function test_can_get_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();
        $total = ReactionTotal::factory()->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $assertTotal = $reactant->getReactionTotal();

        $this->assertTrue($total->is($assertTotal));
    }

    public function test_can_get_null_object_reaction_total(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();

        $assertTotal = $reactant->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $assertTotal);
    }

    public function test_can_get_null_object_reaction_total_with_same_reactant(): void
    {
        Event::fake(); // Prevent total auto creation
        $reactant = Reactant::factory()->create();

        $assertTotal = $reactant->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $assertTotal);
        $this->assertSame($reactant, $assertTotal->getReactant());
    }

    public function test_can_check_is_reacted_by_reacter(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = Reacter::factory()->create();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = $reactant->isReactedBy($reacter);

        $this->assertTrue($isReacted);
    }

    public function test_can_check_is_reacted_by_reacter_when_reacter_is_null_object(): void
    {
        $reacter = new NullReacter(new Bot());
        $reactant = Reactant::factory()->create();

        $isReacted = $reactant->isReactedBy($reacter);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_is_reacted_by_reacter_when_reacter_is_not_persisted(): void
    {
        $reacter = new Reacter();
        $reactant = Reactant::factory()->create();

        $isReacted = $reactant->isReactedBy($reacter);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = Reacter::factory()->create();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isNotReacted = $reactant->isNotReactedBy($reacter);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_when_reacter_is_null_object(): void
    {
        $reacter = new NullReacter(new Bot());
        $reactant = Reactant::factory()->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_when_reacter_is_not_persisted(): void
    {
        $reacter = new Reacter();
        $reactant = Reactant::factory()->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_type(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = Reacter::factory()->create();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = $reactant->isReactedBy($reacter, $reactionType);

        $this->assertTrue($isReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_type_when_reacter_is_null_object(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = new NullReacter(new Bot());
        $reactant = Reactant::factory()->create();

        $isReacted = $reactant->isReactedBy($reacter, $reactionType);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_type_when_reacter_is_not_persisted(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = new Reacter();
        $reactant = Reactant::factory()->create();

        $isReacted = $reactant->isReactedBy($reacter, $reactionType);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_type(): void
    {
        $reactionType = ReactionType::factory()->create();
        $otherReactionType = ReactionType::factory()->create();
        $reacter = Reacter::factory()->create();
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

        $isNotReacted = $reactant->isNotReactedBy($reacter, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_type_when_reacter_is_null_object(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = new NullReacter(new Bot());
        $reactant = Reactant::factory()->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_type_when_reacter_is_not_persisted(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = new Reacter();
        $reactant = Reactant::factory()->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_rate(): void
    {
        $reacter = Reacter::factory()->create();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => 2.0,
        ]);

        $isReacted = $reactant->isReactedBy($reacter, null, 2.0);

        $this->assertTrue($isReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_rate_when_reacter_is_null_object(): void
    {
        $reacter = new NullReacter(new Bot());
        $reactant = Reactant::factory()->create();

        $isReacted = $reactant->isReactedBy($reacter, null, 2.0);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_rate_when_reacter_is_not_persisted(): void
    {
        $reacter = new Reacter();
        $reactant = Reactant::factory()->create();

        $isReacted = $reactant->isReactedBy($reacter, null, 2.0);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_rate(): void
    {
        $reacter = Reacter::factory()->create();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => 4.2,
        ]);
        Reaction::factory()->create([
            'reactant_id' => $reactant->getId(),
            'rate' => 2.0,
        ]);

        $isNotReacted = $reactant->isNotReactedBy($reacter, null, 2.0);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_rate_when_reacter_is_null_object(): void
    {
        $reacter = new NullReacter(new Bot());
        $reactant = Reactant::factory()->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter, null, 2.0);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_rate_when_reacter_is_not_persisted(): void
    {
        $reacter = new Reacter();
        $reactant = Reactant::factory()->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter, null, 2.0);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_type_and_rate(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = Reacter::factory()->create();
        $reactant = Reactant::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
            'rate' => 2.0,
        ]);

        $isReacted = $reactant->isReactedBy($reacter, $reactionType, 2.0);

        $this->assertTrue($isReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_type_and_rate_when_reacter_is_null_object(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = new NullReacter(new Bot());
        $reactant = Reactant::factory()->create();

        $isReacted = $reactant->isReactedBy($reacter, $reactionType, 2.0);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_is_reacted_by_reacter_with_type_and_rate_when_reacter_is_not_persisted(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = new Reacter();
        $reactant = Reactant::factory()->create();

        $isReacted = $reactant->isReactedBy($reacter, $reactionType, 2.0);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_type_and_rate(): void
    {
        $reactionType = ReactionType::factory()->create();
        $otherReactionType = ReactionType::factory()->create();
        $reacter = Reacter::factory()->create();
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

        $isNotReacted = $reactant->isNotReactedBy($reacter, $reactionType, 2.0);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_type_and_rate_when_reacter_is_null_object(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = new NullReacter(new Bot());
        $reactant = Reactant::factory()->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter, $reactionType, 2.0);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_not_reacted_by_reacter_with_type_and_rate_when_reacter_is_not_persisted(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reacter = new Reacter();
        $reactant = Reactant::factory()->create();

        $isNotReacted = $reactant->isNotReactedBy($reacter, $reactionType, 2.0);

        $this->assertTrue($isNotReacted);
    }

    public function test_can_check_is_equal_to_self(): void
    {
        $reactant = Reactant::factory()->create();

        $this->assertTrue($reactant->isEqualTo($reactant));
    }

    public function test_can_check_is_equal_to_other_reactant(): void
    {
        $reactant = Reactant::factory()->create();
        $otherReactant = Reactant::factory()->create();

        $this->assertFalse($reactant->isEqualTo($otherReactant));
    }

    public function test_can_check_is_equal_to_null_object_reactant(): void
    {
        $reactant = Reactant::factory()->create();
        $nullReactant = new NullReactant(new Article());

        $this->assertFalse($reactant->isEqualTo($nullReactant));
    }

    public function test_can_check_is_equal_to_null_object_reactant_when_not_null_object_not_persisted(): void
    {
        $reactant = new Reactant();
        $nullReactant = new NullReactant(new Article());

        $this->assertFalse($reactant->isEqualTo($nullReactant));
    }

    public function test_can_check_is_not_equal_to_self(): void
    {
        $reactant = Reactant::factory()->create();

        $this->assertFalse($reactant->isNotEqualTo($reactant));
    }

    public function test_can_check_is_not_equal_to_other_reactant(): void
    {
        $reactant = Reactant::factory()->create();
        $otherReactant = Reactant::factory()->create();

        $this->assertTrue($reactant->isNotEqualTo($otherReactant));
    }

    public function test_can_check_is_not_equal_to_null_object_reactant(): void
    {
        $reactant = Reactant::factory()->create();
        $nullReactant = new NullReactant(new Article());

        $this->assertTrue($reactant->isNotEqualTo($nullReactant));
    }

    public function test_can_check_is_not_equal_to_null_object_reactant_when_not_null_object_not_persisted(): void
    {
        $reactant = new Reactant();
        $nullReactant = new NullReactant(new Article());

        $this->assertTrue($reactant->isNotEqualTo($nullReactant));
    }

    public function test_can_create_reaction_counter_of_type(): void
    {
        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();

        $reactant->createReactionCounterOfType($reactionType);

        $counters = $reactant->getReactionCounters();
        $this->assertCount(1, $counters);
        /** @var \Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter $counter */
        $counter = $counters[0];
        $this->assertTrue($counter->isReactionOfType($reactionType));
        $this->assertTrue($reactant->is($counter->getReactant()));
        $this->assertSame(0, $counter->getCount());
        $this->assertSame(0.0, $counter->getWeight());
    }

    public function test_throw_exception_on_can_create_reaction_counter_of_type_when_counter_of_same_type_already_exists(): void
    {
        $this->expectException(ReactionCounterDuplicate::class);

        $reactant = Reactant::factory()->create();
        $reactionType = ReactionType::factory()->create();

        $reactant->createReactionCounterOfType($reactionType);
        $reactant->createReactionCounterOfType($reactionType);
    }

    public function test_can_create_reaction_total(): void
    {
        /** @var Reactant $reactant */
        $reactant = Reactant::factory()->create();

        $reactant->createReactionTotal();

        $total = $reactant->getReactionTotal();
        $this->assertTrue($reactant->is($total->getReactant()));
        $this->assertSame(0, $total->getCount());
        $this->assertSame(0.0, $total->getWeight());
    }

    public function test_throw_exception_on_can_create_reaction_total_when_total_already_exists(): void
    {
        $this->expectException(ReactionTotalDuplicate::class);

        $reactant = Reactant::factory()->create();

        $reactant->createReactionTotal();
        $reactant->createReactionTotal();
    }

    public function test_can_check_is_null(): void
    {
        $reactant = Reactant::factory()->create();

        $this->assertFalse($reactant->isNull());
    }

    public function test_can_check_is_null_when_reactant_not_persisted(): void
    {
        $reactant = new Reactant();

        $this->assertTrue($reactant->isNull());
    }

    public function test_can_check_is_not_null(): void
    {
        $reactant = Reactant::factory()->create();

        $this->assertTrue($reactant->isNotNull());
    }

    public function test_can_check_is_not_null_when_reactant_not_persisted(): void
    {
        $reactant = new Reactant();

        $this->assertFalse($reactant->isNotNull());
    }
}
