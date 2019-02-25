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

namespace Cog\Tests\Laravel\Love\Unit\Reacter\Models;

use Cog\Contracts\Love\Reactant\Exceptions\ReactantInvalid;
use Cog\Contracts\Love\Reacter\Exceptions\NotAssignedToReacterable;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionAlreadyExists;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionNotExists;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TypeError;

final class ReacterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fill_type(): void
    {
        $reacter = new Reacter([
            'type' => 'TestType',
        ]);

        $this->assertSame('TestType', $reacter->getAttribute('type'));
    }

    /** @test */
    public function it_casts_id_to_string(): void
    {
        $reacter = factory(Reacter::class)->make([
            'id' => 4,
        ]);

        $this->assertSame('4', $reacter->getAttribute('id'));
    }

    /** @test */
    public function it_can_morph_to_reacterable(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacter->reacterable->is($reacterable));
    }

    /** @test */
    public function it_can_has_reaction(): void
    {
        $reacter = factory(Reacter::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter->getId(),
        ]);

        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    /** @test */
    public function it_can_has_many_reactions(): void
    {
        $reacter = factory(Reacter::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reacter_id' => $reacter->getId(),
        ]);

        $assertReactions = $reacter->reactions;
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_get_id(): void
    {
        $reacter = factory(Reacter::class)->make([
            'id' => '4',
        ]);

        $this->assertSame('4', $reacter->getId());
    }

    /** @test */
    public function it_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reacter = new Reacter();

        $reacter->getId();
    }

    /** @test */
    public function it_can_get_reacterable(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacter->getReacterable()->is($reacterable));
    }

    /** @test */
    public function it_can_throw_exception_on_get_reacterable_when_not_assigned_to_any_reacterable(): void
    {
        $this->expectException(NotAssignedToReacterable::class);

        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacter->getReacterable();
    }

    /** @test */
    public function it_can_get_reactions(): void
    {
        $reacter = factory(Reacter::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reacter_id' => $reacter->getId(),
        ]);

        $assertReactions = $reacter->getReactions();
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_react_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->loveReactant;

        $reacter->reactTo($reactant, $reactionType);

        $this->assertCount(1, $reacter->reactions);
        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->reactant->is($reactant));
    }

    /** @test */
    public function it_can_react_to_reactant_which_reacter_too(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactable = factory(User::class)->create();
        $reactant = $reactable->loveReactant;

        $reacter->reactTo($reactant, $reactionType);

        $this->assertCount(1, $reacter->reactions);
        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->reactant->is($reactant));
    }

    /** @test */
    public function it_throws_reactant_invalid_on_react_to_when_reactant_is_null_object(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = new NullReactant(new Article());

        $reacter->reactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_throws_reactant_invalid_on_react_to_when_reactant_is_not_persisted(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = Reactant::query()->make();

        $reacter->reactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_can_unreact_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reactant_id' => $reactant->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $reacter->unreactTo($reactant, $reactionType);

        $this->assertCount(0, $reacter->reactions);
        $this->assertFalse($reaction->exists());
    }

    /** @test */
    public function it_throws_reactant_invalid_on_unreact_to_when_reactant_is_null_object(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = new NullReactant(new Article());

        $reacter->unreactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_throws_reactant_invalid_on_unreact_to_when_reactant_is_not_persisted(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = Reactant::query()->make();

        $reacter->unreactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_cannot_duplicate_reactions(): void
    {
        $this->expectException(ReactionAlreadyExists::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reacter->reactTo($reactant, $reactionType);
        $reacter->reactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_cannot_unreact_reactant_if_not_reacted(): void
    {
        $this->expectException(ReactionNotExists::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reacter_id' => $reacter->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reactant_id' => $reactant->getId(),
        ]);

        $reacter->unreactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_can_check_is_equal_to_self(): void
    {
        $reacter = factory(Reacter::class)->create();

        $this->assertTrue($reacter->isEqualTo($reacter));
    }

    /** @test */
    public function it_can_check_is_equal_to_other_reacter(): void
    {
        $reacter = factory(Reacter::class)->create();
        $otherReacter = factory(Reacter::class)->create();

        $this->assertFalse($reacter->isEqualTo($otherReacter));
    }

    /** @test */
    public function it_can_check_is_equal_to_null_object_reacter(): void
    {
        $reacter = factory(Reacter::class)->create();
        $nullReacter = new NullReacter(new User());

        $this->assertFalse($reacter->isEqualTo($nullReacter));
    }

    /** @test */
    public function it_can_check_is_equal_to_null_object_reacter_when_not_null_object_not_persisted(): void
    {
        $reacter = new Reacter();
        $nullReacter = new NullReacter(new User());

        $this->assertFalse($reacter->isEqualTo($nullReacter));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_self(): void
    {
        $reacter = factory(Reacter::class)->create();

        $this->assertFalse($reacter->isNotEqualTo($reacter));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_other_reacter(): void
    {
        $reacter = factory(Reacter::class)->create();
        $otherReacter = factory(Reacter::class)->create();

        $this->assertTrue($reacter->isNotEqualTo($otherReacter));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_null_object_reacter(): void
    {
        $reacter = factory(Reacter::class)->create();
        $nullReacter = new NullReacter(new User());

        $this->assertTrue($reacter->isNotEqualTo($nullReacter));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_null_object_reacter_when_not_null_object_not_persisted(): void
    {
        $reacter = new Reacter();
        $nullReacter = new NullReacter(new User());

        $this->assertTrue($reacter->isNotEqualTo($nullReacter));
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = $reacter->isReactedTo($reactant);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactant_when_reactant_is_null_object(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reactant = new NullReactant(new Article());

        $isReacted = $reacter->isReactedTo($reactant);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactant_when_reactant_is_not_persisted(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reactant = Reactant::query()->make();

        $isReacted = $reacter->isReactedTo($reactant);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactant(): void
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

        $isNotReacted = $reacter->isNotReactedTo($reactant);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactant_when_reactant_is_null_object(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reactant = new NullReactant(new Article());

        $isNotReacted = $reacter->isNotReactedTo($reactant);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactant_when_reactant_is_not_persisted(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reactant = Reactant::query()->make();

        $isNotReacted = $reacter->isNotReactedTo($reactant);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactant_with_type(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = $reacter->isReactedToWithType($reactant, $reactionType);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactant_with_type_when_reactant_is_null_object(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = new NullReactant(new Article());

        $isReacted = $reacter->isReactedToWithType($reactant, $reactionType);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactant_with_type_when_reactant_is_not_persisted(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = Reactant::query()->make();

        $isReacted = $reacter->isReactedToWithType($reactant, $reactionType);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactant_with_type(): void
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

        $isNotReacted = $reacter->isNotReactedToWithType($reactant, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactant_with_type_when_reactant_is_null_object(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = new NullReactant(new Article());

        $isNotReacted = $reacter->isNotReactedToWithType($reactant, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactant_with_type_when_reactant_is_not_persisted(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = Reactant::query()->make();

        $isNotReacted = $reacter->isNotReactedToWithType($reactant, $reactionType);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_null(): void
    {
        $reacter = factory(Reacter::class)->create();

        $this->assertFalse($reacter->isNull());
    }

    /** @test */
    public function it_can_check_is_null_when_reacter_not_persisted(): void
    {
        $reacter = new Reacter();

        $this->assertTrue($reacter->isNull());
    }

    /** @test */
    public function it_can_check_is_not_null(): void
    {
        $reacter = factory(Reacter::class)->create();

        $this->assertTrue($reacter->isNotNull());
    }

    /** @test */
    public function it_can_check_is_not_null_when_reacter_not_persisted(): void
    {
        $reacter = new Reacter();

        $this->assertFalse($reacter->isNotNull());
    }
}
