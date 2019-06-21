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

namespace Cog\Tests\Laravel\Love\Unit\Reacter\Facades;

use Cog\Contracts\Love\Reactant\Exceptions\ReactantInvalid;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionAlreadyExists;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionNotExists;
use Cog\Contracts\Love\ReactionType\Exceptions\ReactionTypeInvalid;
use Cog\Laravel\Love\Reacter\Facades\Reacter as ReacterFacade;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\ArticleWithoutAutoReactantCreate;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;

final class ReacterTest extends TestCase
{
    /** @test */
    public function it_can_get_reactions(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reactions = factory(Reaction::class, 2)->create([
            'reacter_id' => $reacter->getId(),
        ]);
        $reacterFacade = new ReacterFacade($reacter);

        $assertReactions = $reacterFacade->getReactions();

        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_react_to_reactable(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $reacterFacade->reactTo($reactable, $reactionType->getName());

        $this->assertCount(1, $reacter->reactions);
        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->reactant->reactable->is($reactable));
    }

    /** @test */
    public function it_can_react_to_reactable_which_reacterable_too(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(User::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $reacterFacade->reactTo($reactable, $reactionType->getName());

        $this->assertCount(1, $reacter->reactions);
        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->reactant->reactable->is($reactable));
    }

    /** @test */
    public function it_throws_reaction_type_invalid_on_react_to_when_reaction_type_not_exist(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(User::class)->create();

        $reacterFacade->reactTo($reactable, 'NotExist');
    }

    /** @test */
    public function it_throws_reactant_invalid_on_react_to_when_reactable_not_registered_as_reactant(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(ArticleWithoutAutoReactantCreate::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $reacterFacade->reactTo($reactable, $reactionType->getName());
    }

    /** @test */
    public function it_throws_reactant_invalid_on_react_to_when_reactable_is_not_persisted(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = new Article();
        $reactionType = factory(ReactionType::class)->create();

        $reacterFacade->reactTo($reactable, $reactionType->getName());
    }

    /** @test */
    public function it_cannot_duplicate_reactions(): void
    {
        $this->expectException(ReactionAlreadyExists::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $reacterFacade->reactTo($reactable, $reactionType->getName());
        $reacterFacade->reactTo($reactable, $reactionType->getName());
    }

    /** @test */
    public function it_can_unreact_to_reactable(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        $reactant = $reactable->getLoveReactant();
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reactant_id' => $reactant->getId(),
            'reacter_id' => $reacter->getId(),
        ]);

        $reacterFacade->unreactTo($reactable, $reactionType->getName());

        $this->assertCount(0, $reacter->reactions);
        $this->assertFalse($reaction->exists());
    }

    /** @test */
    public function it_throws_reaction_type_invalid_on_unreact_to_when_reaction_type_not_exist(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();

        $reacterFacade->unreactTo($reactable, 'NotExist');
    }

    /** @test */
    public function it_throws_reactant_invalid_on_unreact_to_when_reactable_is_not_registered_as_reactant(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(ArticleWithoutAutoReactantCreate::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $reacterFacade->unreactTo($reactable, $reactionType->getName());
    }

    /** @test */
    public function it_throws_reactant_invalid_on_unreact_to_when_reactable_is_not_persisted(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = new Article();
        $reactionType = factory(ReactionType::class)->create();

        $reacterFacade->unreactTo($reactable, $reactionType->getName());
    }

    /** @test */
    public function it_cannot_unreact_reactable_if_not_reacted(): void
    {
        $this->expectException(ReactionNotExists::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->getLoveReactant();
        $reactionType = factory(ReactionType::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reacter_id' => $reacter->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reactant_id' => $reactant->getId(),
        ]);

        $reacterFacade->unreactTo($reactable, $reactionType->getName());
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactable(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->getLoveReactant();
        $reactionType = factory(ReactionType::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = $reacterFacade->isReactedTo($reactable);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactable_when_reactable_is_not_registered_as_reactant(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(ArticleWithoutAutoReactantCreate::class)->create();

        $isReacted = $reacterFacade->isReactedTo($reactable);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactable_when_reactable_is_not_persisted(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = new Article();

        $isReacted = $reacterFacade->isReactedTo($reactable);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactable(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->getLoveReactant();
        $reactionType = factory(ReactionType::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isNotReacted = $reacterFacade->isNotReactedTo($reactable);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactable_when_reactable_is_not_registered_as_reactant(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(ArticleWithoutAutoReactantCreate::class)->create();

        $isNotReacted = $reacterFacade->isNotReactedTo($reactable);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactable_when_reactable_is_not_persisted(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = new Article();

        $isNotReacted = $reacterFacade->isNotReactedTo($reactable);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactable_with_type(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->getLoveReactant();
        $reactionType = factory(ReactionType::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isReacted = $reacterFacade->isReactedTo($reactable, $reactionType->getName());

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactable_with_type_when_reactable_is_not_registered_as_reactant(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(ArticleWithoutAutoReactantCreate::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $isReacted = $reacterFacade->isReactedTo($reactable, $reactionType->getName());

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactable_with_type_when_reactable_is_not_persisted(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = new Article();
        $reactionType = factory(ReactionType::class)->create();

        $isReacted = $reacterFacade->isReactedTo($reactable, $reactionType->getName());

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_throws_reaction_type_invalid_on_is_reacted_to_with_type_when_reaction_type_not_exist(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();

        $reacterFacade->isReactedTo($reactable, 'NotExist');
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactable_with_type(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->getLoveReactant();
        $reactionType = factory(ReactionType::class)->create();
        $otherReactionType = factory(ReactionType::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $otherReactionType->getId(),
            'reacter_id' => $reacter->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $isNotReacted = $reacterFacade->isNotReactedTo($reactable, $reactionType->getName());

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactable_with_type_when_reactable_is_not_registered_as_reactant(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(ArticleWithoutAutoReactantCreate::class)->create();
        $reactionType = factory(ReactionType::class)->create();

        $isNotReacted = $reacterFacade->isNotReactedTo($reactable, $reactionType->getName());

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactable_with_type_when_reactable_is_not_persisted(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = new Article();
        $reactionType = factory(ReactionType::class)->create();

        $isNotReacted = $reacterFacade->isNotReactedTo($reactable, $reactionType->getName());

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_throws_reaction_type_invalid_on_is_not_reacted_to_with_type_when_reaction_type_not_exist(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $reacter = factory(Reacter::class)->create();
        $reacterFacade = new ReacterFacade($reacter);
        $reactable = factory(Article::class)->create();

        $reacterFacade->isNotReactedTo($reactable, 'NotExist');
    }
}
