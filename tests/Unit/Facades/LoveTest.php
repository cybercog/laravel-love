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
    public function it_can_check_is_reaction_of_type_name(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
        ]);

        $true = Love::isReactionOfTypeName($reaction, $reactionType1->getName());
        $false = Love::isReactionOfTypeName($reaction, $reactionType2->getName());

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_check_is_reaction_not_of_type_name(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
        ]);

        $false = Love::isReactionNotOfTypeName($reaction, $reactionType1->getName());
        $true = Love::isReactionNotOfTypeName($reaction, $reactionType2->getName());

        $this->assertFalse($false);
        $this->assertTrue($true);
    }

    /** @test */
    public function it_throws_invalid_reaction_type_exception_on_check_is_reaction_of_type_name_when_type_name_in_unknown(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reaction = factory(Reaction::class)->create();

        Love::isReactionOfTypeName($reaction, 'UnknownType');
    }

    /** @test */
    public function it_throws_invalid_reaction_type_exception_on_check_is_reaction_not_of_type_name_when_type_name_in_unknown(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reaction = factory(Reaction::class)->create();

        Love::isReactionNotOfTypeName($reaction, 'UnknownType');
    }

    /** @test */
    public function it_can_check_is_reacterable_reacted_to_reactable_with_type_name(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactant->getReactable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactant->getReactable(),
            $reactionType2->getName()
        );

        $this->assertTrue($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_reacted_to_reactable_with_type_name_when_reacterable_is_null(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableReactedToWithTypeName(
            null,
            $reactant->getReactable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReacterableReactedToWithTypeName(
            null,
            $reactant->getReactable(),
            $reactionType2->getName()
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_reacted_to_reactable_with_type_name_when_reacterable_has_null_reacter(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableReactedToWithTypeName(
            $reacterable,
            $reactant->getReactable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReacterableReactedToWithTypeName(
            $reacterable,
            $reactant->getReactable(),
            $reactionType2->getName()
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_reacted_to_reactable_with_type_name_when_null_reactant(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $isReacted = Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactable,
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactable,
            $reactionType2->getName()
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_not_reacted_to_reactable_with_type_name(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactant->getReactable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactant->getReactable(),
            $reactionType2->getName()
        );

        $this->assertFalse($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_not_reacted_to_reactable_with_type_name_when_reacterable_is_null(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableNotReactedToWithTypeName(
            null,
            $reactant->getReactable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReacterableNotReactedToWithTypeName(
            null,
            $reactant->getReactable(),
            $reactionType2->getName()
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_not_reacted_to_reactable_with_type_name_when_reacterable_has_null_reacter(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacterable,
            $reactant->getReactable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacterable,
            $reactant->getReactable(),
            $reactionType2->getName()
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_not_reacted_to_reactable_with_type_name_when_reactable_has_null_reactant(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $isReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactable,
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactable,
            $reactionType2->getName()
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_reacted_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant1 = factory(Reactant::class)->states('withReactable')->create();
        $reactant2 = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant2->getId(),
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
    public function it_can_check_is_reacterable_reacted_to_reactable_when_reacterable_is_null(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableReactedTo(
            null,
            $reactant->getReactable()
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_reacted_to_reactable_when_reacterable_has_null_reacter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableReactedTo(
            $reacterable,
            $reactant->getReactable()
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_reacted_to_reactable_when_reactable_has_null_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $isReacted = Love::isReacterableReactedTo(
            $reacter->getReacterable(),
            $reactable
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_not_reacted_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant1 = factory(Reactant::class)->states('withReactable')->create();
        $reactant2 = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant2->getId(),
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
    public function it_can_check_is_reacterable_not_reacted_to_reactable_when_reacterable_is_null(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableNotReactedTo(
            null,
            $reactant->getReactable()
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_not_reacted_to_reactable_when_reacterable_has_null_reacter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReacterableNotReactedTo(
            $reacterable,
            $reactant->getReactable()
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacterable_not_reacted_to_reactable_when_reactable_has_null_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $isReacted = Love::isReacterableNotReactedTo(
            $reacter->getReacterable(),
            $reactable
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_throws_reaction_type_invalid_exception_on_is_reacterable_reacted_to_reactable_with_type_name_when_type_unknown(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();

        Love::isReacterableReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactant->getReactable(),
            'UnknownType'
        );
    }

    /** @test */
    public function it_throws_reaction_type_invalid_exception_on_is_reacterable_not_reacted_to_reactable_with_type_name_when_type_unknown(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();

        Love::isReacterableNotReactedToWithTypeName(
            $reacter->getReacterable(),
            $reactant->getReactable(),
            'UnknownType'
        );
    }

    /** @test */
    public function it_can_check_is_reactable_reacted_by_reacterable_with_type_name(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableReactedByWithTypeName(
            $reactant->getReactable(),
            $reacter->getReacterable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReactableReactedByWithTypeName(
            $reactant->getReactable(),
            $reacter->getReacterable(),
            $reactionType2->getName()
        );

        $this->assertTrue($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_reacted_by_reacterable_with_type_name_when_reacterable_is_null(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableReactedByWithTypeName(
            $reactant->getReactable(),
            null,
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReactableReactedByWithTypeName(
            $reactant->getReactable(),
            null,
            $reactionType2->getName()
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_reacted_by_reacterable_with_type_name_when_reacterable_has_null_reacter(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableReactedByWithTypeName(
            $reactant->getReactable(),
            $reacterable,
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReactableReactedByWithTypeName(
            $reactant->getReactable(),
            $reacterable,
            $reactionType2->getName()
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_reacted_by_reacterable_with_type_name_when_null_reactant(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $isReacted = Love::isReactableReactedByWithTypeName(
            $reactable,
            $reacter->getReacterable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReactableReactedByWithTypeName(
            $reactable,
            $reacter->getReacterable(),
            $reactionType2->getName()
        );

        $this->assertFalse($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_not_reacted_by_reacterable_with_type_name(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableNotReactedByWithTypeName(
            $reactant->getReactable(),
            $reacter->getReacterable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReactableNotReactedByWithTypeName(
            $reactant->getReactable(),
            $reacter->getReacterable(),
            $reactionType2->getName()
        );

        $this->assertFalse($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_not_reacted_by_reacterable_with_type_name_when_reacterable_is_null(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableNotReactedByWithTypeName(
            $reactant->getReactable(),
            null,
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReactableNotReactedByWithTypeName(
            $reactant->getReactable(),
            null,
            $reactionType2->getName()
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_not_reacted_by_reacterable_with_type_name_when_reacterable_has_null_reacter(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableNotReactedByWithTypeName(
            $reactant->getReactable(),
            $reacterable,
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReactableNotReactedByWithTypeName(
            $reactant->getReactable(),
            $reacterable,
            $reactionType2->getName()
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_not_reacted_by_reacterable_with_type_name_when_reactable_has_null_reactant(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $isReacted = Love::isReactableNotReactedByWithTypeName(
            $reactable,
            $reacter->getReacterable(),
            $reactionType1->getName()
        );
        $isNotReacted = Love::isReactableNotReactedByWithTypeName(
            $reactable,
            $reacter->getReacterable(),
            $reactionType2->getName()
        );

        $this->assertTrue($isReacted);
        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_reacted_by_reacterable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant1 = factory(Reactant::class)->states('withReactable')->create();
        $reactant2 = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant2->getId(),
        ]);

        $isReacted = Love::isReactableReactedBy(
            $reactant1->getReactable(),
            $reacter->getReacterable()
        );
        $isNotReacted = Love::isReactableReactedBy(
            $reactant2->getReactable(),
            $reacter->getReacterable()
        );

        $this->assertTrue($isReacted);
        $this->assertFalse($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_reacted_by_reacterable_when_reacterable_is_null(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableReactedBy(
            $reactant->getReactable(),
            null
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_reacted_by_reacterable_when_reacterable_has_null_reacter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableReactedBy(
            $reactant->getReactable(),
            $reacterable
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_reacted_by_reacterable_when_reactable_has_null_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $isReacted = Love::isReactableReactedBy(
            $reactable,
            $reacter->getReacterable()
        );

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_not_reacted_by_reacterable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant1 = factory(Reactant::class)->states('withReactable')->create();
        $reactant2 = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant2->getId(),
        ]);

        $isNotReacted = Love::isReactableNotReactedBy(
            $reactant1->getReactable(),
            $reacter->getReacterable()
        );
        $isReacted = Love::isReactableNotReactedBy(
            $reactant2->getReactable(),
            $reacter->getReacterable()
        );

        $this->assertTrue($isNotReacted);
        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_not_reacted_by_reacterable_when_reacterable_is_null(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableNotReactedBy(
            $reactant->getReactable(),
            null
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_not_reacted_by_reacterable_when_reacterable_has_null_reacter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacterable = new User();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = Love::isReactableNotReactedBy(
            $reactant->getReactable(),
            $reacterable
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reactable_not_reacted_by_reacterable_when_reactable_has_null_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $isReacted = Love::isReactableNotReactedBy(
            $reactable,
            $reacter->getReacterable()
        );

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_throws_reaction_type_invalid_exception_on_is_reactable_reacted_by_reacterable_with_type_name_when_type_unknown(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();

        Love::isReactableReactedByWithTypeName(
            $reactant->getReactable(),
            $reacter->getReacterable(),
            'UnknownType'
        );
    }

    /** @test */
    public function it_throws_reaction_type_invalid_exception_on_is_reactable_not_reacted_by_reacterable_with_type_name_when_type_unknown(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->states('withReacterable')->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();

        Love::isReactableNotReactedByWithTypeName(
            $reactant->getReactable(),
            $reacter->getReacterable(),
            'UnknownType'
        );
    }

    /** @test */
    public function it_can_get_reactable_reactions_count_for_type_name(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $count = Love::getReactableReactionsCountForTypeName(
            $reactant->getReactable(),
            $reactionType->getName()
        );

        $this->assertSame(3, $count);
    }

    /** @test */
    public function it_can_get_reactable_reactions_count_for_type_name_when_reactable_reactant_is_null(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
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
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $count = Love::getReactableReactionsCountForTypeName(
            $reactant->getReactable(),
            $reactionType2->getName()
        );

        $this->assertSame(0, $count);
    }

    /** @test */
    public function it_throws_exception_on_invalid_reaction_type_in_get_reactable_reactions_count_for_type_name(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
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
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $weight = Love::getReactableReactionsWeightForTypeName(
            $reactant->getReactable(),
            $reactionType->getName()
        );

        $this->assertSame(6, $weight);
    }

    /** @test */
    public function it_can_get_reactable_reactions_weight_for_type_name_when_reactable_reactant_is_null(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
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
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $weight = Love::getReactableReactionsWeightForTypeName(
            $reactant->getReactable(),
            $reactionType2->getName()
        );

        $this->assertSame(0, $weight);
    }

    /** @test */
    public function it_throws_exception_on_invalid_reaction_type_in_get_reactable_reactions_weight_for_type_name(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        Love::getReactableReactionsWeightForTypeName(
            $reactant->getReactable(),
            'InvalidType'
        );
    }

    /** @test */
    public function it_can_get_reactable_reactions_total_count(): void
    {
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $count = Love::getReactableReactionsTotalCount(
            $reactant->getReactable()
        );

        $this->assertSame(3, $count);
    }

    /** @test */
    public function it_can_get_reactable_reactions_total_count_when_reactable_reactant_is_null(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
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
            'name' => 'Like',
            'weight' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => 3,
        ]);
        $reactant = factory(Reactant::class)->states('withReactable')->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $weight = Love::getReactableReactionsTotalWeight(
            $reactant->getReactable()
        );

        $this->assertSame(7, $weight);
    }

    /** @test */
    public function it_can_get_reactable_reactions_total_weight_when_reactable_reactant_is_null(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactable = new Article();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $weight = Love::getReactableReactionsTotalWeight(
            $reactable
        );

        $this->assertSame(0, $weight);
    }
}
