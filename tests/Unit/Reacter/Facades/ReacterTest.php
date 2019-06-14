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
use Cog\Laravel\Love\Reacter\Facades\Reacter as ReacterFacade;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\ArticleWithoutAutoReactantCreate;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;

final class ReacterTest extends TestCase
{
    /** @test */
    public function it_can_react_to_reacterable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $article = factory(Article::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reacterFacade = (new ReacterFacade($reacter));

        $reacterFacade->reactTo($article, $reactionType->getName());

        $this->assertCount(1, $reacter->reactions);
        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->reactant->is($article->getLoveReactant()));
    }

    /** @test */
    public function it_can_react_to_reactable(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactable = factory(Article::class)->create();
        $reacterFacade = (new ReacterFacade($reacter));

        $reacterFacade->reactTo($reactable, $reactionType->getName());

        $this->assertCount(1, $reacter->reactions);
        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->reactant->reactable->is($reactable));
    }

    /** @test */
    public function it_can_react_to_reactable_which_reacterable_too(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactable = factory(User::class)->create();
        $reacterFacade = (new ReacterFacade($reacter));

        $reacterFacade->reactTo($reactable, $reactionType->getName());

        $this->assertCount(1, $reacter->reactions);
        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->reactant->reactable->is($reactable));
    }

    /** @test */
    public function it_throws_reactant_invalid_on_react_to_when_reactable_not_registered_as_reactant(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactable = factory(ArticleWithoutAutoReactantCreate::class)->create();
        $reacterFacade = (new ReacterFacade($reacter));

        $reacterFacade->reactTo($reactable, $reactionType->getName());
    }

    /** @test */
    public function it_throws_reactant_invalid_on_react_to_when_reactable_is_not_persisted(): void
    {
        $this->expectException(ReactantInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactable = new Article();
        $reacterFacade = (new ReacterFacade($reacter));

        $reacterFacade->reactTo($reactable, $reactionType->getName());
    }
}
