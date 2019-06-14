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

use Cog\Laravel\Love\Reacter\Facades\Reacter as ReacterFacade;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
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
}
