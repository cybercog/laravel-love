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

namespace Cog\Tests\Laravel\Love\Unit\Facades;

use Cog\Contracts\Love\ReactionType\Exceptions\ReactionTypeInvalid;
use Cog\Laravel\Love\Facades\Love;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class LoveTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_check_is_reaction_type_name(): void
    {
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getKey(),
        ]);

        $true = Love::isReactionTypeName($reaction, $reactionType1->getName());
        $false = Love::isReactionTypeName($reaction, $reactionType2->getName());

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_throw_exception_on_invalid_reaction_type_in_check_is_reaction_type_name(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reaction = factory(Reaction::class)->create();

        Love::isReactionTypeName($reaction, 'InvalidType');
    }

    /** @test */
    public function it_can_check_is_reaction_not_type_name(): void
    {
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getKey(),
        ]);

        $false = Love::isReactionNotTypeName($reaction, $reactionType1->getName());
        $true = Love::isReactionNotTypeName($reaction, $reactionType2->getName());

        $this->assertFalse($false);
        $this->assertTrue($true);
    }

    /** @test */
    public function it_throw_exception_on_invalid_reaction_type_in_check_is_reaction_not_type_name(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reaction = factory(Reaction::class)->create();

        Love::isReactionNotTypeName($reaction, 'InvalidType');
    }

    /** @test */
    public function it_can_check_if_reacterable_reacted_with_type_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactionType->getName(),
            $reactant->getReactable()
        );
        $isNotReacted = Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $otherReactionType->getName(),
            $reactant->getReactable()
        );

        $this->assertTrue($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_if_null_reacted_with_type_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableReactedToWithTypeName(
            null,
            $reactionType->getName(),
            $reactant->getReactable()
        );
        $isNotReacted = Love::isReacterableReactedToWithTypeName(
            null,
            $otherReactionType->getName(),
            $reactant->getReactable()
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_with_null_reacter_reacted_with_type_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableReactedToWithTypeName(
            $reacterable,
            $reactionType->getName(),
            $reactant->getReactable()
        );
        $isNotReacted = Love::isReacterableReactedToWithTypeName(
            $reacterable,
            $otherReactionType->getName(),
            $reactant->getReactable()
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_reacted_with_type_to_reactable_with_null_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
        ]);

        $isReacted = Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactionType->getName(),
            $reactable
        );
        $isNotReacted = Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $otherReactionType->getName(),
            $reactable
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_throw_exception_on_check_if_unknown_type_passed_to_reacterable_reacted_with_type_to_reactable(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();

        Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            'UnknownType',
            $reactant->getReactable()
        );
    }

    /** @test */
    public function it_can_check_if_reacterable_not_reacted_with_type_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactionType->getName(),
            $reactant->getReactable()
        );
        $isNotReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $otherReactionType->getName(),
            $reactant->getReactable()
        );

        $this->assertFalse($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_if_null_not_reacted_with_type_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableNotReactedToWithTypeName(
            null,
            $reactionType->getName(),
            $reactant->getReactable()
        );
        $isNotReacted = Love::isReacterableNotReactedToWithTypeName(
            null,
            $otherReactionType->getName(),
            $reactant->getReactable()
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_with_null_reacter_not_reacted_with_type_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacterable,
            $reactionType->getName(),
            $reactant->getReactable()
        );
        $isNotReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacterable,
            $otherReactionType->getName(),
            $reactant->getReactable()
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_not_reacted_with_type_to_reactable_with_null_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
        ]);

        $isReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactionType->getName(),
            $reactable
        );
        $isNotReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $otherReactionType->getName(),
            $reactable
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_reacted_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant1 = factory(Reactant::class)->states('withReactable')->create();
        $reactant2 = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant1->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant2->getKey(),
        ]);

        $isReacted = Love::isReacterableReactedTo(
            $reacter->getReacterable(),
            $reactant1->getReactable()
        );
        $isNotReacted = Love::isReacterableReactedTo(
            $reacter->getReacterable(),
            $reactant2->getReactable()
        );

        $this->assertTrue($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_if_null_reacted_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableReactedTo(
            null,
            $reactant->getReactable()
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_with_null_reacter_reacted_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableReactedTo(
            $reacterable,
            $reactant->getReactable()
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_reacted_to_reactable_with_null_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
        ]);

        $isReacted = Love::isReacterableReactedTo(
            $reacter->getReacterable(),
            $reactable
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_not_reacted_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant1 = factory(Reactant::class)->states('withReactable')->create();
        $reactant2 = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant1->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant2->getKey(),
        ]);

        $isNotReacted = Love::isReacterableNotReactedTo(
            $reacter->getReacterable(),
            $reactant1->getReactable()
        );
        $isReacted = Love::isReacterableNotReactedTo(
            $reacter->getReacterable(),
            $reactant2->getReactable()
        );

        $this->assertTrue($isNotReacted);
        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_if_null_not_reacted_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableNotReactedTo(
            null,
            $reactant->getReactable()
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_with_null_reacter_not_reacted_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = Love::isReacterableNotReactedTo(
            $reacterable,
            $reactant->getReactable()
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_if_reacterable_not_reacted_to_reactable_with_null_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
        ]);

        $isReacted = Love::isReacterableNotReactedTo(
            $reacter->getReacterable(),
            $reactable
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_get_reactable_reactions_count_for_type_name(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $count = Love::getReactableReactionsCountForTypeName(
            $reactant->getReactable(),
            $reactionType->getName()
        );

        $this->assertSame(3, $count);
    }

    /** @test */
    public function it_can_get_reactable_with_null_reactant_reactions_count_for_type_name(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
        ]);

        $count = Love::getReactableReactionsCountForTypeName(
            $reactable,
            $reactionType->getName()
        );

        $this->assertSame(0, $count);
    }

    /** @test */
    public function it_can_get_reactable_reactions_count_for_type_name_if_no_reactions_exists(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $count = Love::getReactableReactionsCountForTypeName(
            $reactant->getReactable(),
            $otherReactionType->getName()
        );

        $this->assertSame(0, $count);
    }

    /** @test */
    public function it_throw_exception_on_invalid_reaction_type_in_get_reactable_reactions_count_for_type_name(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        Love::getReactableReactionsCountForTypeName(
            $reactant->getReactable(),
            'InvalidType'
        );
    }

    /** @test */
    public function it_can_get_reactable_reactions_weight_for_type_name(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $weight = Love::getReactableReactionsWeightForTypeName(
            $reactant->getReactable(),
            $reactionType->getName()
        );

        $this->assertSame(6, $weight);
    }

    /** @test */
    public function it_can_get_reactable_with_null_reactant_reactions_weight_for_type_name(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
        ]);

        $weight = Love::getReactableReactionsWeightForTypeName(
            $reactable,
            $reactionType->getName()
        );

        $this->assertSame(0, $weight);
    }

    /** @test */
    public function it_can_get_reactable_reactions_weight_for_type_name_if_no_reactions_exists(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $weight = Love::getReactableReactionsWeightForTypeName(
            $reactant->getReactable(),
            $otherReactionType->getName()
        );

        $this->assertSame(0, $weight);
    }

    /** @test */
    public function it_throw_exception_on_invalid_reaction_type_in_get_reactable_reactions_weight_for_type_name(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        Love::getReactableReactionsWeightForTypeName(
            $reactant->getReactable(),
            'InvalidType'
        );
    }

    /** @test */
    public function it_can_get_reactable_reactions_total_count(): void
    {
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $count = Love::getReactableReactionsTotalCount(
            $reactant->getReactable()
        );

        $this->assertSame(3, $count);
    }

    /** @test */
    public function it_can_get_reactable_with_null_reactant_reactions_total_count(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
        ]);

        $count = Love::getReactableReactionsTotalCount(
            $reactable
        );

        $this->assertSame(0, $count);
    }

    /** @test */
    public function it_can_get_reactable_reactions_total_weight(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'weight' => 3,
        ]);
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $weight = Love::getReactableReactionsTotalWeight(
            $reactant->getReactable()
        );

        $this->assertSame(7, $weight);
    }

    /** @test */
    public function it_can_get_reactable_with_null_reactant_reactions_total_weight(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
        ]);

        $weight = Love::getReactableReactionsTotalWeight(
            $reactable
        );

        $this->assertSame(0, $weight);
    }
}
