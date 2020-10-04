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

namespace Cog\Tests\Laravel\Love\Unit\Reactable\Observers;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\ArticleWithoutAutoReactantCreate;
use Cog\Tests\Laravel\Love\TestCase;

final class ReactableObserverTest extends TestCase
{
    /** @test */
    public function it_creates_reactant_on_created(): void
    {
        $article = factory(Article::class)->create();

        $this->assertInstanceOf(ReactantInterface::class, $article->getLoveReactant());
    }

    /** @test */
    public function it_not_creates_new_reactant_on_created_if_already_exist(): void
    {
        $reactant = factory(Reactant::class)->create();
        $article = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertSame(1, Reactant::query()->count());
        $this->assertTrue($article->getLoveReactant()->is($reactant));
    }

    /** @test */
    public function it_not_creates_new_reactant_on_created_if_opted_out(): void
    {
        $article = factory(ArticleWithoutAutoReactantCreate::class)->create();

        $this->assertSame(0, Reactant::query()->count());
        $this->assertInstanceOf(NullReactant::class, $article->getLoveReactant());
    }
}
