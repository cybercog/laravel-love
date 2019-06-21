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

namespace Cog\Tests\Laravel\Love\Unit\Reactable\Models\Traits;

use Cog\Contracts\Love\Reactable\Exceptions\AlreadyRegisteredAsLoveReactant;
use Cog\Laravel\Love\Reactant\Facades\Reactant as ReactantFacade;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReactableTest extends TestCase
{
    /** @test */
    public function it_can_belong_to_love_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactable->loveReactant->is($reactant));
    }

    /** @test */
    public function it_can_get_love_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactable->getLoveReactant()->is($reactant));
    }

    /** @test */
    public function it_can_get_reactant_null_object_when_reactant_is_null(): void
    {
        $reactable = new Article();

        $reactant = $reactable->getLoveReactant();

        $this->assertInstanceOf(NullReactant::class, $reactant);
    }

    /** @test */
    public function it_can_get_reactant_facade(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $reactantFacade = $reactable->akaLoveReactant();

        $this->assertInstanceOf(ReactantFacade::class, $reactantFacade);
    }

    /** @test */
    public function it_can_get_reactant_facade_when_reactant_is_null(): void
    {
        $reactable = new Article();

        $reactantFacade = $reactable->akaLoveReactant();

        $this->assertInstanceOf(ReactantFacade::class, $reactantFacade);
    }

    /** @test */
    public function it_register_reactable_as_reactant_on_create(): void
    {
        $reactable = new Article([
            'name' => 'Test Article',
        ]);
        $reactable->save();

        $this->assertTrue($reactable->isRegisteredAsLoveReactant());
        $this->assertInstanceOf(Reactant::class, $reactable->getLoveReactant());
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
        $reactable->setAttribute('love_reactant_id', $reactant->getId());
        $reactable->save();

        $this->assertSame(1, Reactant::query()->count());
        $this->assertTrue($reactable->isRegisteredAsLoveReactant());
        $this->assertInstanceOf(Reactant::class, $reactable->getLoveReactant());
    }

    /** @test */
    public function it_can_check_if_registered_as_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $notRegisteredReactable = new Article();
        $registeredReactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($registeredReactable->isRegisteredAsLoveReactant());
        $this->assertFalse($notRegisteredReactable->isRegisteredAsLoveReactant());
    }

    /** @test */
    public function it_can_check_if_not_registered_as_reactant(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $notRegisteredReactable = new Article();
        $registeredReactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertFalse($registeredReactable->isNotRegisteredAsLoveReactant());
        $this->assertTrue($notRegisteredReactable->isNotRegisteredAsLoveReactant());
    }

    /** @test */
    public function it_can_register_as_love_reactant(): void
    {
        Event::fake();
        $article = factory(Article::class)->create();

        $article->registerAsLoveReactant();

        $this->assertInstanceOf(Reactant::class, $article->getLoveReactant());
    }

    /** @test */
    public function it_throws_exception_on_register_as_love_reactant_when_already_registered(): void
    {
        $this->expectException(AlreadyRegisteredAsLoveReactant::class);

        $article = factory(Article::class)->create();

        $article->registerAsLoveReactant();
    }

    /** @test */
    public function it_can_scope_by_reacter(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacter1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacter2->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacter1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacter2->getId(),
        ]);

        $reactedByReacter1 = Article::query()
            ->whereReactedBy($reacter1)
            ->get();
        $reactedByReacter2 = Article::query()
            ->whereReactedBy($reacter2)
            ->get();

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
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacter1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacter2->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacter1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacter2->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacter1->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacter2->getId(),
        ]);

        $reactedByReacter1WithReactionType1 = Article::query()
            ->whereReactedByWithType($reacter1, $reactionType1)
            ->get();
        $reactedByReacter1WithReactionType2 = Article::query()
            ->whereReactedByWithType($reacter1, $reactionType2)
            ->get();
        $reactedByReacter2WithReactionType1 = Article::query()
            ->whereReactedByWithType($reacter2, $reactionType1)
            ->get();
        $reactedByReacter2WithReactionType2 = Article::query()
            ->whereReactedByWithType($reacter2, $reactionType2)
            ->get();

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
    public function it_can_get_reactables_join_reaction_counter_with_type(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'weight' => 1,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 5)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 6)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->joinReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'desc')
            ->get();

        $assertAsc = [
            ['name' => $reactable3->name, 'reactions_count' => '1', 'reactions_weight' => '2'],
            ['name' => $reactable1->name, 'reactions_count' => '2', 'reactions_weight' => '4'],
            ['name' => $reactable2->name, 'reactions_count' => '3', 'reactions_weight' => '6'],
        ];
        $assertDesc = array_reverse($assertAsc);
        $this->assertSame($assertAsc, $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->name,
                'reactions_count' => $reactable->reactions_count,
                'reactions_weight' => $reactable->reactions_weight,
            ];
        })->toArray());
        $this->assertSame($assertDesc, $reactablesOrderedDesc->map(function (Article $reactable) {
            return [
                'name' => $reactable->name,
                'reactions_count' => $reactable->reactions_count,
                'reactions_weight' => $reactable->reactions_weight,
            ];
        })->toArray());
    }

    /** @test */
    public function it_select_default_reactable_columns_on_get_reactable_join_reaction_counter_with_type(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'asc')
            ->get();

        $this->assertSame([
            $reactable3->id => $reactable3->name,
            $reactable1->id => $reactable1->name,
            $reactable2->id => $reactable2->name,
        ], $reactablesOrderedAsc->pluck('name', 'id')->toArray());
    }

    /** @test */
    public function it_can_select_custom_reactable_columns_on_get_reactable_join_reactions_count_with_type(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->select('name')
            ->joinReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'asc')
            ->get();

        $this->assertSame([
            ['name' => $reactable3->name, 'reactions_count' => '1', 'reactions_weight' => '2'],
            ['name' => $reactable1->name, 'reactions_count' => '2', 'reactions_weight' => '4'],
            ['name' => $reactable2->name, 'reactions_count' => '3', 'reactions_weight' => '6'],
        ], $reactablesOrderedAsc->toArray());
    }

    /** @test */
    public function it_can_order_by_reactions_total_count(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionTotal()
            ->orderBy('reactions_total_count', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->joinReactionTotal()
            ->orderBy('reactions_total_count', 'desc')
            ->get();

        $this->assertSame(['1', '2', '4'], $reactablesOrderedAsc->pluck('reactions_total_count')->toArray());
        $this->assertSame(['4', '2', '1'], $reactablesOrderedDesc->pluck('reactions_total_count')->toArray());
    }

    /** @test */
    public function it_select_default_reactable_columns_on_order_by_reactions_total_count(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionTotal()
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
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->select('name')
            ->joinReactionTotal()
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
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionTotal()
            ->orderBy('reactions_total_weight', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->joinReactionTotal()
            ->orderBy('reactions_total_weight', 'desc')
            ->get();

        $this->assertSame(['2', '4', '8'], $reactablesOrderedAsc->pluck('reactions_total_weight')->toArray());
        $this->assertSame(['8', '4', '2'], $reactablesOrderedDesc->pluck('reactions_total_weight')->toArray());
    }

    /** @test */
    public function it_select_default_reactable_columns_on_order_by_reactions_weight(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionTotal()
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
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->select('name')
            ->joinReactionTotal()
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
    public function it_chain_join_reaction_counter_with_type_and_join_reaction_total(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'weight' => 1,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 4)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 1)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1)
            ->joinReactionTotal()
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

    /** @test */
    public function it_include_reaction_total_null_values_replaced_with_zero(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'weight' => 1,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionTotal()
            ->orderBy('reactions_total_weight', 'asc')
            ->get();

        $this->assertSame([
            [
                'name' => $reactable2->name,
                'reactions_total_count' => '0',
                'reactions_total_weight' => '0',
            ],
            [
                'name' => $reactable3->name,
                'reactions_total_count' => '1',
                'reactions_total_weight' => '1',
            ],
            [
                'name' => $reactable1->name,
                'reactions_total_count' => '2',
                'reactions_total_weight' => '4',
            ],
        ], $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->name,
                'reactions_total_count' => $reactable->reactions_total_count,
                'reactions_total_weight' => $reactable->reactions_total_weight,
            ];
        })->toArray());
    }

    /** @test */
    public function it_include_reaction_counter_null_values_replaced_with_zero(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'weight' => 1,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1)
            ->orderBy('reactions_count', 'asc')
            ->get();

        $this->assertSame([
            [
                'name' => $reactable2->name,
                'reactions_count' => '0',
            ],
            [
                'name' => $reactable3->name,
                'reactions_count' => '1',
            ],
            [
                'name' => $reactable1->name,
                'reactions_count' => '2',
            ],
        ], $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->name,
                'reactions_count' => $reactable->reactions_count,
            ];
        })->toArray());
    }

    /** @test */
    public function it_include_null_counter_and_total_values_in_chain_join_reaction_counter_with_type_and_join_reaction_total(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'weight' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'weight' => 1,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1)
            ->joinReactionTotal()
            ->orderBy('reactions_total_weight', 'asc')
            ->get();

        $this->assertSame([
            [
                'name' => $reactable2->name,
                'reactions_count' => '0',
                'reactions_total_count' => '0',
                'reactions_total_weight' => '0',
            ],
            [
                'name' => $reactable3->name,
                'reactions_count' => '0',
                'reactions_total_count' => '1',
                'reactions_total_weight' => '1',
            ],
            [
                'name' => $reactable1->name,
                'reactions_count' => '2',
                'reactions_total_count' => '2',
                'reactions_total_weight' => '4',
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
