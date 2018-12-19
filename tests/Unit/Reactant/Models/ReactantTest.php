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
use Cog\Laravel\Love\Reactant\ReactionTotality\Models\NullReactionTotality;
use Cog\Laravel\Love\Reactant\ReactionTotality\Models\ReactionTotality;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

final class ReactantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fill_type(): void
    {
        $reactant = new Reactant([
            'type' => 'TestType',
        ]);

        $this->assertSame('TestType', $reactant->type);
    }

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
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertReaction = $reactant->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    /** @test */
    public function it_can_has_many_reactions(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
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
    public function it_can_has_reaction_totality(): void
    {
        Event::fake(); // Prevent totality auto creation
        $reactant = factory(Reactant::class)->create();

        $totality = factory(ReactionTotality::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reactant->reactionTotality->is($totality));
    }

    /** @test */
    public function it_can_get_reactable(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reactant->getReactable()->is($reactable));
    }

    /** @test */
    public function it_can_get_reactions(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertReactions = $reactant->getReactions();
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_get_reaction_counters(): void
    {
        $reactant = factory(Reactant::class)->create();

        $counters = factory(ReactionCounter::class, 2)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertCounters = $reactant->getReactionCounters();
        $this->assertTrue($assertCounters->get(0)->is($counters->get(0)));
        $this->assertTrue($assertCounters->get(1)->is($counters->get(1)));
    }

    /** @test */
    public function it_can_get_reaction_totality(): void
    {
        Event::fake(); // Prevent totality auto creation
        $reactant = factory(Reactant::class)->create();

        $totality = factory(ReactionTotality::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reactant->getReactionTotality()->is($totality));
    }

    /** @test */
    public function it_can_get_null_reaction_totality(): void
    {
        Event::fake(); // Prevent totality auto creation
        $reactant = factory(Reactant::class)->create();

        $this->assertInstanceOf(NullReactionTotality::class, $reactant->getReactionTotality());
    }

    /** @test */
    public function it_can_get_null_reaction_totality_with_same_reactant(): void
    {
        Event::fake(); // Prevent totality auto creation
        $reactant = factory(Reactant::class)->create();

        $this->assertInstanceOf(NullReactionTotality::class, $reactant->getReactionTotality());
        $this->assertSame($reactant, $reactant->getReactionTotality()->getReactant());
    }
}
