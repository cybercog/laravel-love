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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\ReactionSummary;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_morph_to_reactable(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reactant->reactable->is($reactable));
    }

    /** @test */
    public function it_can_has_reaction(): void
    {
        $reactant = factory(Reactant::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertReaction = $reactant->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    /** @test */
    public function it_can_has_many_reactions(): void
    {
        $reactant = factory(Reactant::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertReactions = $reactant->reactions;
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_has_reaction_counter(): void
    {
        $reactant = factory(Reactant::class)->create();

        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertCounter = $reactant->reactionCounters->first();
        $this->assertTrue($assertCounter->is($counter));
    }

    /** @test */
    public function it_can_has_many_reaction_counters(): void
    {
        $reactant = factory(Reactant::class)->create();

        $counters = factory(ReactionCounter::class, 2)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertCounters = $reactant->reactionCounters;
        $this->assertTrue($assertCounters->get(0)->is($counters->get(0)));
        $this->assertTrue($assertCounters->get(1)->is($counters->get(1)));
    }

    /** @test */
    public function it_can_has_reaction_summary(): void
    {
        $reactant = factory(Reactant::class)->create();

        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertSummary = $reactant->reactionSummary()->first();
        $this->assertTrue($assertSummary->is($summary));
    }
}
