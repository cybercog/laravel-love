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
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
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
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reactable->getReactant()->is($reactant));
    }

    /** @test */
    public function it_register_reactable_as_reactant_on_create(): void
    {
        $reactable = new Article([
            'name' => 'Test Article',
        ]);
        $reactable->save();

        $this->assertTrue($reactable->isRegisteredAsReactant());
        $this->assertInstanceOf(Reactant::class, $reactable->getReactant());
    }

    /** @test */
    public function it_not_create_new_reactant_if_manually_registered_reactable_as_reactant_on_create(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $reactable = new Article([
            'name' => 'Test Article',
        ]);
        $reactable->setAttribute('love_reactant_id', $reactant->getKey());
        $reactable->save();

        $this->assertSame(1, Reactant::query()->count());
        $this->assertTrue($reactable->isRegisteredAsReactant());
        $this->assertInstanceOf(Reactant::class, $reactable->getReactant());
    }

    /** @test */
    public function it_can_determine_if_registered_as_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $notRegisteredReactable = new Article();
        $registeredReactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($registeredReactable->isRegisteredAsReactant());
        $this->assertFalse($notRegisteredReactable->isRegisteredAsReactant());
    }

    /** @test */
    public function it_can_determine_if_not_registered_as_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $notRegisteredReactable = new Article();
        $registeredReactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getKey(),
        ]);

        $this->assertFalse($registeredReactable->isNotRegisteredAsReactant());
        $this->assertTrue($notRegisteredReactable->isNotRegisteredAsReactant());
    }

    /** @test */
    public function it_can_scope_by_reacter(): void
    {
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        factory(Reaction::class)->create([
            'reactant_id' => $reactable1->getReactant()->getKey(),
            'reacter_id' => $reacter1->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reactant_id' => $reactable2->getReactant()->getKey(),
            'reacter_id' => $reacter2->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reactant_id' => $reactable3->getReactant()->getKey(),
            'reacter_id' => $reacter1->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reactant_id' => $reactable3->getReactant()->getKey(),
            'reacter_id' => $reacter2->getKey(),
        ]);

        $reactedByReacter1 = Article::whereReactedBy($reacter1)->get();
        $reactedByReacter2 = Article::whereReactedBy($reacter2)->get();

        $this->assertSame([
            $reactable1->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacter1->pluck('id')->toArray());
        $this->assertSame([
            $reactable2->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacter2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_scope_by_reacter_and_reaction_type(): void
    {
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1,
            'reactant_id' => $reactable1->getReactant()->getKey(),
            'reacter_id' => $reacter1->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2,
            'reactant_id' => $reactable1->getReactant()->getKey(),
            'reacter_id' => $reacter2->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2,
            'reactant_id' => $reactable2->getReactant()->getKey(),
            'reacter_id' => $reacter1->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1,
            'reactant_id' => $reactable2->getReactant()->getKey(),
            'reacter_id' => $reacter2->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1,
            'reactant_id' => $reactable3->getReactant()->getKey(),
            'reacter_id' => $reacter1->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1,
            'reactant_id' => $reactable3->getReactant()->getKey(),
            'reacter_id' => $reacter2->getKey(),
        ]);

        $reactedByReacter1WithReactionType1 = Article::whereReactedWithTypeBy($reacter1, $reactionType1)->get();
        $reactedByReacter1WithReactionType2 = Article::whereReactedWithTypeBy($reacter1, $reactionType2)->get();
        $reactedByReacter2WithReactionType1 = Article::whereReactedWithTypeBy($reacter2, $reactionType1)->get();
        $reactedByReacter2WithReactionType2 = Article::whereReactedWithTypeBy($reacter2, $reactionType2)->get();

        $this->assertSame([
            $reactable1->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacter1WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reactable2->getKey(),
        ], $reactedByReacter1WithReactionType2->pluck('id')->toArray());
        $this->assertSame([
            $reactable2->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacter2WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reactable1->getKey(),
        ], $reactedByReacter2WithReactionType2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_order_by_reactions_count_of_type(): void
    {
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable1->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable2->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable3->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType2->getKey(),
            'reactant_id' => $reactable1->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 5)->create([
            'reaction_type_id' => $reactionType2->getKey(),
            'reactant_id' => $reactable2->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 6)->create([
            'reaction_type_id' => $reactionType2->getKey(),
            'reactant_id' => $reactable3->getReactant()->getKey(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->withReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->withReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'desc')
            ->get();

        $this->assertSame(['1', '2', '3'], $reactablesOrderedAsc->pluck('reactions_count')->toArray());
        $this->assertSame(['3', '2', '1'], $reactablesOrderedDesc->pluck('reactions_count')->toArray());
    }

    /** @test */
    public function it_select_default_reactable_columns_on_order_by_reactions_count_of_type(): void
    {
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reactionType1 = factory(ReactionType::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable1->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable2->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable3->getReactant()->getKey(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->withReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'asc')
            ->get();

        $this->assertSame([
            $reactable3->id => $reactable3->name,
            $reactable1->id => $reactable1->name,
            $reactable2->id => $reactable2->name,
        ], $reactablesOrderedAsc->pluck('name', 'id')->toArray());
    }

    /** @test */
    public function it_can_select_custom_reactable_columns_on_order_by_reactions_count_of_type(): void
    {
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reactionType1 = factory(ReactionType::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable1->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable2->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable3->getReactant()->getKey(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->select('name')
            ->withReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'asc')
            ->get();

        $this->assertSame([
            ['name' => $reactable3->name, 'reactions_count' => '1'],
            ['name' => $reactable1->name, 'reactions_count' => '2'],
            ['name' => $reactable2->name, 'reactions_count' => '3'],
        ], $reactablesOrderedAsc->toArray());
    }

    /** @test */
    public function it_can_order_by_reactions_total_count(): void
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

        $reactablesOrderedAsc = Article::query()
            ->withReactionSummary()
            ->orderBy('reactions_total_count', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->withReactionSummary()
            ->orderBy('reactions_total_count', 'desc')
            ->get();

        $this->assertSame(['1', '2', '4'], $reactablesOrderedAsc->pluck('reactions_total_count')->toArray());
        $this->assertSame(['4', '2', '1'], $reactablesOrderedDesc->pluck('reactions_total_count')->toArray());
    }

    /** @test */
    public function it_select_default_reactable_columns_on_order_by_reactions_total_count(): void
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

        $reactablesOrderedAsc = Article::query()
            ->withReactionSummary()
            ->orderBy('reactions_total_count', 'asc')
            ->get();

        $this->assertSame([
            $reactable3->id => $reactable3->name,
            $reactable1->id => $reactable1->name,
            $reactable2->id => $reactable2->name,
        ], $reactablesOrderedAsc->pluck('name', 'id')->toArray());
    }

    /** @test */
    public function it_can_select_custom_reactable_columns_on_order_by_reactions_total_count(): void
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

        $reactablesOrderedAsc = Article::query()
            ->select('name')
            ->withReactionSummary()
            ->orderBy('reactions_total_count', 'asc')
            ->get();

        $this->assertSame([
            ['name' => $reactable3->name, 'reactions_total_count' => '1'],
            ['name' => $reactable1->name, 'reactions_total_count' => '2'],
            ['name' => $reactable2->name, 'reactions_total_count' => '4'],
        ], $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->name,
                'reactions_total_count' => $reactable->reactions_total_count,
            ];
        })->toArray());
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

        $reactablesOrderedAsc = Article::query()
            ->withReactionSummary()
            ->orderBy('reactions_total_weight', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->withReactionSummary()
            ->orderBy('reactions_total_weight', 'desc')
            ->get();

        $this->assertSame(['2', '4', '8'], $reactablesOrderedAsc->pluck('reactions_total_weight')->toArray());
        $this->assertSame(['8', '4', '2'], $reactablesOrderedDesc->pluck('reactions_total_weight')->toArray());
    }

    /** @test */
    public function it_select_default_reactable_columns_on_order_by_reactions_weight(): void
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

        $reactablesOrderedAsc = Article::query()
            ->withReactionSummary()
            ->orderBy('reactions_total_weight', 'asc')
            ->get();

        $this->assertSame([
            $reactable3->id => $reactable3->name,
            $reactable1->id => $reactable1->name,
            $reactable2->id => $reactable2->name,
        ], $reactablesOrderedAsc->pluck('name', 'id')->toArray());
    }

    /** @test */
    public function it_can_select_custom_reactable_columns_on_order_by_reactions_weight(): void
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

        $reactablesOrderedAsc = Article::query()
            ->select('name')
            ->withReactionSummary()
            ->orderBy('reactions_total_weight', 'asc')
            ->get();

        $this->assertSame([
            ['name' => $reactable3->name, 'reactions_total_weight' => '2'],
            ['name' => $reactable1->name, 'reactions_total_weight' => '4'],
            ['name' => $reactable2->name, 'reactions_total_weight' => '8'],
        ], $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->name,
                'reactions_total_weight' => $reactable->reactions_total_weight,
            ];
        })->toArray());
    }

    /** @test */
    public function it_chain_with_reaction_counter_of_type_and_with_reaction_summary(): void
    {
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reactionType1 = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'weight' => 1,
        ]);
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable1->getReactant()->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getKey(),
            'reactant_id' => $reactable1->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable2->getReactant()->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getKey(),
            'reactant_id' => $reactable2->getReactant()->getKey(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType1->getKey(),
            'reactant_id' => $reactable3->getReactant()->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getKey(),
            'reactant_id' => $reactable3->getReactant()->getKey(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->withReactionCounterOfType($reactionType1)
            ->withReactionSummary()
            ->orderBy('reactions_total_weight', 'asc')
            ->get();

        $this->assertSame([
            [
                'name' => $reactable3->name,
                'reactions_count' => '1',
                'reactions_total_count' => '2',
                'reactions_total_weight' => '3',
            ],
            [
                'name' => $reactable1->name,
                'reactions_count' => '2',
                'reactions_total_count' => '3',
                'reactions_total_weight' => '5',
            ],
            [
                'name' => $reactable2->name,
                'reactions_count' => '4',
                'reactions_total_count' => '5',
                'reactions_total_weight' => '9',
            ],
        ], $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->name,
                'reactions_count' => $reactable->reactions_count,
                'reactions_total_count' => $reactable->reactions_total_count,
                'reactions_total_weight' => $reactable->reactions_total_weight,
            ];
        })->toArray());
    }
}
