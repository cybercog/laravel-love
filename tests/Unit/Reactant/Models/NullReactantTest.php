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

use Cog\Contracts\Love\Reactant\Exceptions\ReactantInvalid;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Collection;
use TypeError;

final class NullReactantTest extends TestCase
{
    /** @test */
    public function it_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reactant = new NullReactant(new Article());

        $reactant->getId();
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);

        $assertReactable = $reactant->getReactable();

        $this->assertSame($reactable, $assertReactable);
    }

    /** @test */
    public function it_can_get_reactions(): void
    {
        $reactant = new NullReactant(new Article());

        $reactions = $reactant->getReactions();

        $this->assertCount(0, $reactions);
        $this->assertIsIterable($reactions);
    }

    /** @test */
    public function it_can_get_reactions_collection(): void
    {
        $reactant = new NullReactant(new Article());

        $reactions = $reactant->getReactions();

        $this->assertInstanceOf(Collection::class, $reactions);
    }

    /** @test */
    public function it_can_get_reaction_counters(): void
    {
        $reactant = new NullReactant(new Article());

        $counters = $reactant->getReactionCounters();

        $this->assertCount(0, $counters);
        $this->assertIsIterable($counters);
    }

    /** @test */
    public function it_can_get_reaction_counters_collection(): void
    {
        $reactant = new NullReactant(new Article());

        $counters = $reactant->getReactionCounters();

        $this->assertInstanceOf(Collection::class, $counters);
    }

    /** @test */
    public function it_can_get_reaction_counter_of_type_null_object_when_reaction_counter_not_exist(): void
    {
        $reactant = new NullReactant(new Article());
        $reactionType = factory(ReactionType::class)->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
    }

    /** @test */
    public function it_can_get_reaction_counter_of_type_null_object_with_same_reactant(): void
    {
        $reactant = new NullReactant(new Article());
        $reactionType = factory(ReactionType::class)->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactant, $counter->getReactant());
    }

    /** @test */
    public function it_can_get_reaction_counter_of_type_null_object_with_same_reaction_type(): void
    {
        $reactant = new NullReactant(new Article());
        $reactionType = factory(ReactionType::class)->create();

        $counter = $reactant->getReactionCounterOfType($reactionType);

        $this->assertInstanceOf(NullReactionCounter::class, $counter);
        $this->assertSame($reactionType, $counter->getReactionType());
    }

    /** @test */
    public function it_can_get_reaction_total_null_object_when_total_not_exist(): void
    {
        $reactant = new NullReactant(new Article());

        $total = $reactant->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $total);
    }

    /** @test */
    public function it_can_get_reaction_total_null_object_with_same_reactant(): void
    {
        $reactant = new NullReactant(new Article());

        $total = $reactant->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $total);
        $this->assertSame($reactant, $total->getReactant());
    }

    /** @test */
    public function it_can_check_is_equal_to_self(): void
    {
        $nullReactant = new NullReactant(new Article());

        $this->assertTrue($nullReactant->isEqualTo($nullReactant));
    }

    /** @test */
    public function it_can_check_is_equal_to_other_null_object_reactant(): void
    {
        $nullReactant = new NullReactant(new Article());
        $otherNullReactant = new NullReactant(new Article());

        $this->assertTrue($nullReactant->isEqualTo($otherNullReactant));
    }

    /** @test */
    public function it_can_check_is_equal_to_not_null_object_reactant(): void
    {
        $nullReactant = new NullReactant(new Article());
        $reactant = factory(Reactant::class)->create();

        $this->assertFalse($nullReactant->isEqualTo($reactant));
    }

    /** @test */
    public function it_can_check_is_equal_to_not_null_object_reactant_and_not_persisted(): void
    {
        $nullReactant = new NullReactant(new Article());
        $reactant = new Reactant();

        $this->assertFalse($nullReactant->isEqualTo($reactant));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_self(): void
    {
        $nullReactant = new NullReactant(new Article());

        $this->assertFalse($nullReactant->isNotEqualTo($nullReactant));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_other_null_object_reactant(): void
    {
        $nullReactant = new NullReactant(new Article());
        $otherNullReactant = new NullReactant(new Article());

        $this->assertFalse($nullReactant->isNotEqualTo($otherNullReactant));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_not_null_object_reactant(): void
    {
        $nullReactant = new NullReactant(new Article());
        $reactant = factory(Reactant::class)->create();

        $this->assertTrue($nullReactant->isNotEqualTo($reactant));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_not_null_object_reactant_and_not_persisted(): void
    {
        $nullReactant = new NullReactant(new Article());
        $reactant = new Reactant();

        $this->assertTrue($nullReactant->isNotEqualTo($reactant));
    }

    /** @test */
    public function it_can_check_is_reacted_by(): void
    {
        $reactant = new NullReactant(new Article());
        $reacter = factory(Reacter::class)->make();

        $isReacted = $reactant->isReactedBy($reacter);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by(): void
    {
        $reactant = new NullReactant(new Article());
        $reacter = factory(Reacter::class)->make();

        $isReacted = $reactant->isNotReactedBy($reacter);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_by_with_type(): void
    {
        $reactant = new NullReactant(new Article());
        $reacter = factory(Reacter::class)->make();
        $reactionType = new ReactionType();

        $isReacted = $reactant->isReactedBy($reacter, $reactionType);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_by_with_type(): void
    {
        $reactant = new NullReactant(new Article());
        $reacter = factory(Reacter::class)->make();
        $reactionType = new ReactionType();

        $isReacted = $reactant->isNotReactedBy($reacter, $reactionType);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_throws_exception_on_create_reaction_counter_of_type(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reactant = new NullReactant(new Article());
        $reactionType = factory(ReactionType::class)->create();

        $reactant->createReactionCounterOfType($reactionType);
    }

    /** @test */
    public function it_throws_exception_on_create_reaction_total(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reactant = new NullReactant(new Article());

        $reactant->createReactionTotal();
    }

    /** @test */
    public function it_can_check_is_null(): void
    {
        $reactant = new NullReactant(new Article());

        $this->assertTrue($reactant->isNull());
    }

    /** @test */
    public function it_can_check_is_not_null(): void
    {
        $reactant = new NullReactant(new Article());

        $this->assertFalse($reactant->isNotNull());
    }
}
