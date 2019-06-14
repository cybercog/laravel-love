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
        $reactionType = factory(ReactionType::class)->create();
        $reactant = $reactable->getLoveReactant();
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
}
