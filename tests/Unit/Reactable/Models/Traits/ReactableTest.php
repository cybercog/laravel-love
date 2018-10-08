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

namespace Cog\Tests\Laravel\Love\Unit\Reactable\Models\Traits;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reactable->reactant->is($reactant));
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reactable->getReactant()->is($reactant));
    }

    /** @test */
    public function it_can_order_by_reactions_weight(): void
    {
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactable1->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactable2->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactable3->getReactant()->getKey(),
        ]);

        $reactablesOrderedAsc = Article::orderByReactionsWeight('asc')->get();
        $reactablesOrderedDesc = Article::orderByReactionsWeight('desc')->get();

        $this->assertSame(['2', '4', '8'], $reactablesOrderedAsc->pluck('total_weight')->toArray());
        $this->assertSame(['8', '4', '2'], $reactablesOrderedDesc->pluck('total_weight')->toArray());
    }

    /** @test */
    public function it_can_order_by_reactions_count(): void
    {
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactable1->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactable2->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactable3->getReactant()->getKey(),
        ]);

        $reactablesOrderedAsc = Article::orderByReactionsCount('asc')->get();
        $reactablesOrderedDesc = Article::orderByReactionsCount('desc')->get();

        $this->assertSame(['1', '2', '4'], $reactablesOrderedAsc->pluck('total_count')->toArray());
        $this->assertSame(['4', '2', '1'], $reactablesOrderedDesc->pluck('total_count')->toArray());
    }
}
