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
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

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

        $reactantFacade = $reactable->viaLoveReactant();

        $this->assertInstanceOf(ReactantFacade::class, $reactantFacade);
    }

    /** @test */
    public function it_can_get_reactant_facade_when_reactant_is_null(): void
    {
        $reactable = new Article();

        $reactantFacade = $reactable->viaLoveReactant();

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
    public function it_can_scope_by_reacterable(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reacterable1 = factory(User::class)->create();
        $reacterable2 = factory(User::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);

        $reactedByReacterable1 = Article::query()
            ->whereReactedBy($reacterable1)
            ->get();
        $reactedByReacterable2 = Article::query()
            ->whereReactedBy($reacterable2)
            ->get();

        $this->assertSame([
            $reactable1->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacterable1->pluck('id')->toArray());
        $this->assertSame([
            $reactable2->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacterable2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_scope_by_reacterable_and_reaction_type(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reacterable1 = factory(User::class)->create();
        $reacterable2 = factory(User::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);

        $reactedByReacterable1WithReactionType1 = Article::query()
            ->whereReactedBy($reacterable1, $reactionType1->getName())
            ->get();
        $reactedByReacterable1WithReactionType2 = Article::query()
            ->whereReactedBy($reacterable1, $reactionType2->getName())
            ->get();
        $reactedByReacterable2WithReactionType1 = Article::query()
            ->whereReactedBy($reacterable2, $reactionType1->getName())
            ->get();
        $reactedByReacterable2WithReactionType2 = Article::query()
            ->whereReactedBy($reacterable2, $reactionType2->getName())
            ->get();

        $this->assertSame([
            $reactable1->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacterable1WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reactable2->getKey(),
        ], $reactedByReacterable1WithReactionType2->pluck('id')->toArray());
        $this->assertSame([
            $reactable2->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacterable2WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reactable1->getKey(),
        ], $reactedByReacterable2WithReactionType2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_scope_not_reacted_by_reacterable(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reacterable1 = factory(User::class)->create();
        $reacterable2 = factory(User::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);

        $reactedByReacterable1 = Article::query()
            ->whereNotReactedBy($reacterable1)
            ->get();
        $reactedByReacterable2 = Article::query()
            ->whereNotReactedBy($reacterable2)
            ->get();

        $this->assertSame([
            $reactable2->getKey(),
        ], $reactedByReacterable1->pluck('id')->toArray());
        $this->assertSame([
            $reactable1->getKey(),
        ], $reactedByReacterable2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_scope_not_reacted_by_reacterable_and_reaction_type(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();
        $reacterable1 = factory(User::class)->create();
        $reacterable2 = factory(User::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);

        $reactedByReacterable1WithReactionType1 = Article::query()
            ->whereNotReactedBy($reacterable1, $reactionType1->getName())
            ->get();
        $reactedByReacterable1WithReactionType2 = Article::query()
            ->whereNotReactedBy($reacterable1, $reactionType2->getName())
            ->get();
        $reactedByReacterable2WithReactionType1 = Article::query()
            ->whereNotReactedBy($reacterable2, $reactionType1->getName())
            ->get();
        $reactedByReacterable2WithReactionType2 = Article::query()
            ->whereNotReactedBy($reacterable2, $reactionType2->getName())
            ->get();

        $this->assertSame([
            $reactable2->getKey(),
        ], $reactedByReacterable1WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reactable1->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacterable1WithReactionType2->pluck('id')->toArray());
        $this->assertSame([
            $reactable1->getKey(),
        ], $reactedByReacterable2WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reactable2->getKey(),
            $reactable3->getKey(),
        ], $reactedByReacterable2WithReactionType2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_get_reactables_join_reaction_counter_with_type(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'mass' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'mass' => 1,
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

        $reactionType1CountKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_count';
        $reactionType1WeightKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_weight';

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->orderBy($reactionType1CountKey, 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->orderBy($reactionType1CountKey, 'desc')
            ->get();

        $assertAsc = [
            ['name' => $reactable3->name, "$reactionType1CountKey" => '1', "$reactionType1WeightKey" => '2'],
            ['name' => $reactable1->name, "$reactionType1CountKey" => '2', "$reactionType1WeightKey" => '4'],
            ['name' => $reactable2->name, "$reactionType1CountKey" => '3', "$reactionType1WeightKey" => '6'],
        ];
        $assertDesc = array_reverse($assertAsc);
        $this->assertSame($assertAsc, $reactablesOrderedAsc->map(function (Article $reactable) use ($reactionType1CountKey, $reactionType1WeightKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
                "$reactionType1WeightKey" => $reactable->{$reactionType1WeightKey},
            ];
        })->toArray());
        $this->assertSame($assertDesc, $reactablesOrderedDesc->map(function (Article $reactable) use ($reactionType1CountKey, $reactionType1WeightKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
                "$reactionType1WeightKey" => $reactable->{$reactionType1WeightKey},
            ];
        })->toArray());
    }

    /** @test */
    public function it_can_get_reactables_join_reaction_counter_with_type_when_type_contains_multiple_words(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'SuperLike',
            'mass' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'mass' => 1,
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

        $reactionType1CountKey = 'reaction_super_like_count';
        $reactionType1WeightKey = 'reaction_super_like_weight';

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->orderBy($reactionType1CountKey, 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->orderBy($reactionType1CountKey, 'desc')
            ->get();

        $assertAsc = [
            ['name' => $reactable3->name, "$reactionType1CountKey" => '1', "$reactionType1WeightKey" => '2'],
            ['name' => $reactable1->name, "$reactionType1CountKey" => '2', "$reactionType1WeightKey" => '4'],
            ['name' => $reactable2->name, "$reactionType1CountKey" => '3', "$reactionType1WeightKey" => '6'],
        ];
        $assertDesc = array_reverse($assertAsc);
        $this->assertSame($assertAsc, $reactablesOrderedAsc->map(function (Article $reactable) use ($reactionType1CountKey, $reactionType1WeightKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
                "$reactionType1WeightKey" => $reactable->{$reactionType1WeightKey},
            ];
        })->toArray());
        $this->assertSame($assertDesc, $reactablesOrderedDesc->map(function (Article $reactable) use ($reactionType1CountKey, $reactionType1WeightKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
                "$reactionType1WeightKey" => $reactable->{$reactionType1WeightKey},
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

        $reactionType1CountKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_count';

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->orderBy($reactionType1CountKey, 'asc')
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
            'mass' => 2,
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

        $reactionType1CountKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_count';
        $reactionType1WeightKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_weight';

        $reactablesOrderedAsc = Article::query()
            ->select('name')
            ->joinReactionCounterOfType($reactionType1->getName())
            ->orderBy($reactionType1CountKey, 'asc')
            ->get();

        $this->assertSame([
            ['name' => $reactable3->name, "$reactionType1CountKey" => '1', "$reactionType1WeightKey" => '2'],
            ['name' => $reactable1->name, "$reactionType1CountKey" => '2', "$reactionType1WeightKey" => '4'],
            ['name' => $reactable2->name, "$reactionType1CountKey" => '3', "$reactionType1WeightKey" => '6'],
        ], $reactablesOrderedAsc->toArray());
    }

    /** @test */
    public function it_can_order_by_total_reactions_count(): void
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
            ->orderBy('reaction_total_count', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->joinReactionTotal()
            ->orderBy('reaction_total_count', 'desc')
            ->get();

        $this->assertSame(['1', '2', '4'], $reactablesOrderedAsc->pluck('reaction_total_count')->toArray());
        $this->assertSame(['4', '2', '1'], $reactablesOrderedDesc->pluck('reaction_total_count')->toArray());
    }

    /** @test */
    public function it_can_order_by_total_reactions_count_with_custom_alias(): void
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
            ->joinReactionTotal('custom_alias')
            ->orderBy('custom_alias_count', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->joinReactionTotal('custom_alias')
            ->orderBy('custom_alias_count', 'desc')
            ->get();

        $this->assertSame(['1', '2', '4'], $reactablesOrderedAsc->pluck('custom_alias_count')->toArray());
        $this->assertSame(['4', '2', '1'], $reactablesOrderedDesc->pluck('custom_alias_count')->toArray());
    }

    /** @test */
    public function it_select_default_reactable_columns_on_order_by_total_reactions_count(): void
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
            ->orderBy('reaction_total_count', 'asc')
            ->get();

        $this->assertSame([
            $reactable3->id => $reactable3->name,
            $reactable1->id => $reactable1->name,
            $reactable2->id => $reactable2->name,
        ], $reactablesOrderedAsc->pluck('name', 'id')->toArray());
    }

    /** @test */
    public function it_can_select_custom_reactable_columns_on_order_by_total_reactions_count(): void
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
            ->orderBy('reaction_total_count', 'asc')
            ->get();

        $this->assertSame([
            ['name' => $reactable3->name, 'reaction_total_count' => '1'],
            ['name' => $reactable1->name, 'reaction_total_count' => '2'],
            ['name' => $reactable2->name, 'reaction_total_count' => '4'],
        ], $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                'reaction_total_count' => $reactable->getAttributeValue('reaction_total_count'),
            ];
        })->toArray());
    }

    /** @test */
    public function it_can_order_by_reactions_weight(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create([
            'mass' => 2,
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
            ->orderBy('reaction_total_weight', 'asc')
            ->get();
        $reactablesOrderedDesc = Article::query()
            ->joinReactionTotal()
            ->orderBy('reaction_total_weight', 'desc')
            ->get();

        $this->assertSame(['2', '4', '8'], $reactablesOrderedAsc->pluck('reaction_total_weight')->toArray());
        $this->assertSame(['8', '4', '2'], $reactablesOrderedDesc->pluck('reaction_total_weight')->toArray());
    }

    /** @test */
    public function it_select_default_reactable_columns_on_order_by_reactions_weight(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create([
            'mass' => 2,
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
            ->orderBy('reaction_total_weight', 'asc')
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
            'mass' => 2,
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
            ->orderBy('reaction_total_weight', 'asc')
            ->get();

        $this->assertSame([
            ['name' => $reactable3->name, 'reaction_total_weight' => '2'],
            ['name' => $reactable1->name, 'reaction_total_weight' => '4'],
            ['name' => $reactable2->name, 'reaction_total_weight' => '8'],
        ], $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                'reaction_total_weight' => $reactable->getAttributeValue('reaction_total_weight'),
            ];
        })->toArray());
    }

    /** @test */
    public function it_chain_join_reaction_counter_with_type_and_join_reaction_total(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'mass' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'mass' => 1,
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

        $reactionType1CountKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_count';

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->joinReactionTotal()
            ->orderBy('reaction_total_weight', 'asc')
            ->get();

        $this->assertSame([
            [
                'name' => $reactable3->name,
                "$reactionType1CountKey" => '1',
                'reaction_total_count' => '2',
                'reaction_total_weight' => '3',
            ],
            [
                'name' => $reactable1->name,
                "$reactionType1CountKey" => '2',
                'reaction_total_count' => '3',
                'reaction_total_weight' => '5',
            ],
            [
                'name' => $reactable2->name,
                "$reactionType1CountKey" => '4',
                'reaction_total_count' => '5',
                'reaction_total_weight' => '9',
            ],
        ], $reactablesOrderedAsc->map(function (Article $reactable) use ($reactionType1CountKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
                'reaction_total_count' => $reactable->getAttributeValue('reaction_total_count'),
                'reaction_total_weight' => $reactable->getAttributeValue('reaction_total_weight'),
            ];
        })->toArray());
    }

    /** @test */
    public function it_include_reaction_total_null_values_replaced_with_zero(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'mass' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'mass' => 1,
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
            ->orderBy('reaction_total_weight', 'asc')
            ->get();

        $this->assertSame([
            [
                'name' => $reactable2->name,
                'reaction_total_count' => '0',
                'reaction_total_weight' => '0',
            ],
            [
                'name' => $reactable3->name,
                'reaction_total_count' => '1',
                'reaction_total_weight' => '1',
            ],
            [
                'name' => $reactable1->name,
                'reaction_total_count' => '2',
                'reaction_total_weight' => '4',
            ],
        ], $reactablesOrderedAsc->map(function (Article $reactable) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                'reaction_total_count' => $reactable->getAttributeValue('reaction_total_count'),
                'reaction_total_weight' => $reactable->getAttributeValue('reaction_total_weight'),
            ];
        })->toArray());
    }

    /** @test */
    public function it_include_reaction_counter_null_values_replaced_with_zero(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'mass' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'mass' => 1,
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

        $reactionType1CountKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_count';

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->orderBy($reactionType1CountKey, 'asc')
            ->get();

        $this->assertSame([
            [
                'name' => $reactable2->name,
                "$reactionType1CountKey" => '0',
            ],
            [
                'name' => $reactable3->name,
                "$reactionType1CountKey" => '1',
            ],
            [
                'name' => $reactable1->name,
                "$reactionType1CountKey" => '2',
            ],
        ], $reactablesOrderedAsc->map(function (Article $reactable) use ($reactionType1CountKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
            ];
        })->toArray());
    }

    /** @test */
    public function it_include_null_counter_and_total_values_in_chain_join_reaction_counter_with_type_and_join_reaction_total(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'mass' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'mass' => 1,
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

        $reactionType1CountKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_count';

        $reactablesOrderedAsc = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->joinReactionTotal()
            ->orderBy('reaction_total_weight', 'asc')
            ->get();

        $this->assertSame([
            [
                'name' => $reactable2->name,
                "$reactionType1CountKey" => '0',
                'reaction_total_count' => '0',
                'reaction_total_weight' => '0',
            ],
            [
                'name' => $reactable3->name,
                "$reactionType1CountKey" => '0',
                'reaction_total_count' => '1',
                'reaction_total_weight' => '1',
            ],
            [
                'name' => $reactable1->name,
                "$reactionType1CountKey" => '2',
                'reaction_total_count' => '2',
                'reaction_total_weight' => '4',
            ],
        ], $reactablesOrderedAsc->map(function (Article $reactable) use ($reactionType1CountKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
                'reaction_total_count' => $reactable->getAttributeValue('reaction_total_count'),
                'reaction_total_weight' => $reactable->getAttributeValue('reaction_total_weight'),
            ];
        })->toArray());
    }

    /** @test */
    public function it_can_chain_multiple_join_reaction_counter_of_type(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
            'mass' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'mass' => -1,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();

        factory(Reaction::class, 5)->create([
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
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 7)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesWithTypeCount = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName())
            ->joinReactionCounterOfType($reactionType2->getName())
            ->joinReactionTotal()
            ->get();

        $reactionType1CountKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_count';
        $reactionType1WeightKey = 'reaction_' . Str::snake($reactionType1->getName()) . '_weight';
        $reactionType2CountKey = 'reaction_' . Str::snake($reactionType2->getName()) . '_count';
        $reactionType2WeightKey = 'reaction_' . Str::snake($reactionType2->getName()) . '_weight';
        $this->assertSame([
            [
                'name' => $reactable1->name,
                "$reactionType1CountKey" => '5',
                "$reactionType1WeightKey" => '10',
                "$reactionType2CountKey" => '1',
                "$reactionType2WeightKey" => '-1',
                'reaction_total_weight' => '9',
            ],
            [
                'name' => $reactable2->name,
                "$reactionType1CountKey" => '4',
                "$reactionType1WeightKey" => '8',
                "$reactionType2CountKey" => '2',
                "$reactionType2WeightKey" => '-2',
                'reaction_total_weight' => '6',
            ],
            [
                'name' => $reactable3->name,
                "$reactionType1CountKey" => '3',
                "$reactionType1WeightKey" => '6',
                "$reactionType2CountKey" => '7',
                "$reactionType2WeightKey" => '-7',
                'reaction_total_weight' => '-1',
            ],
        ], $reactablesWithTypeCount->map(function (Article $reactable) use ($reactionType1WeightKey, $reactionType1CountKey, $reactionType2CountKey, $reactionType2WeightKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
                "$reactionType1WeightKey" => $reactable->{$reactionType1WeightKey},
                "$reactionType2CountKey" => $reactable->{$reactionType2CountKey},
                "$reactionType2WeightKey" => $reactable->{$reactionType2WeightKey},
                'reaction_total_weight' => $reactable->getAttributeValue('reaction_total_weight'),
            ];
        })->toArray());
    }

    /** @test */
    public function it_can_chain_multiple_join_reaction_counter_of_type_with_custom_aliases(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create([
            'name' => 'Like',
            'mass' => 2,
        ]);
        $reactionType2 = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'mass' => -1,
        ]);
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        $reactable3 = factory(Article::class)->create();

        factory(Reaction::class, 5)->create([
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
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);
        factory(Reaction::class, 7)->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable3->getLoveReactant()->getId(),
        ]);

        $reactablesWithTypeCount = Article::query()
            ->joinReactionCounterOfType($reactionType1->getName(), 'likes')
            ->joinReactionCounterOfType($reactionType2->getName(), 'custom_alias')
            ->get();

        $reactionType1CountKey = 'likes_count';
        $reactionType1WeightKey = 'likes_weight';
        $reactionType2CountKey = 'custom_alias_count';
        $reactionType2WeightKey = 'custom_alias_weight';
        $this->assertSame([
            [
                'name' => $reactable1->name,
                "$reactionType1CountKey" => '5',
                "$reactionType1WeightKey" => '10',
                "$reactionType2CountKey" => '1',
                "$reactionType2WeightKey" => '-1',
            ],
            [
                'name' => $reactable2->name,
                "$reactionType1CountKey" => '4',
                "$reactionType1WeightKey" => '8',
                "$reactionType2CountKey" => '2',
                "$reactionType2WeightKey" => '-2',
            ],
            [
                'name' => $reactable3->name,
                "$reactionType1CountKey" => '3',
                "$reactionType1WeightKey" => '6',
                "$reactionType2CountKey" => '7',
                "$reactionType2WeightKey" => '-7',
            ],
        ], $reactablesWithTypeCount->map(function (Article $reactable) use ($reactionType1WeightKey, $reactionType1CountKey, $reactionType2CountKey, $reactionType2WeightKey) {
            return [
                'name' => $reactable->getAttributeValue('name'),
                "$reactionType1CountKey" => $reactable->{$reactionType1CountKey},
                "$reactionType1WeightKey" => $reactable->{$reactionType1WeightKey},
                "$reactionType2CountKey" => $reactable->{$reactionType2CountKey},
                "$reactionType2WeightKey" => $reactable->{$reactionType2WeightKey},
            ];
        })->toArray());
    }
}
