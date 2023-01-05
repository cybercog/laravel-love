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

namespace Cog\Tests\Laravel\Love\Unit\Reacterable\Models\Traits;

use Cog\Contracts\Love\Reacterable\Exceptions\AlreadyRegisteredAsLoveReacter;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Facades\Reacter as ReacterFacade;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReacterableTest extends TestCase
{
    /** @test */
    public function it_can_belong_to_love_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacterable->loveReacter->is($reacter));
    }

    /** @test */
    public function it_can_get_love_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacterable->getLoveReacter()->is($reacter));
    }

    /** @test */
    public function it_can_get_reacter_null_object_when_reacter_is_null(): void
    {
        $reacterable = new User();

        $reacter = $reacterable->getLoveReacter();

        $this->assertInstanceOf(NullReacter::class, $reacter);
    }

    /** @test */
    public function it_can_get_reacter_facade(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $reacterFacade = $reacterable->viaLoveReacter();

        $this->assertInstanceOf(ReacterFacade::class, $reacterFacade);
    }

    /** @test */
    public function it_can_get_reacter_facade_when_reacter_is_null(): void
    {
        $reacterable = new User();

        $reacterFacade = $reacterable->viaLoveReacter();

        $this->assertInstanceOf(ReacterFacade::class, $reacterFacade);
    }

    /** @test */
    public function it_register_reacterable_as_reacter_on_create(): void
    {
        $reacterable = new Bot([
            'name' => 'TestBot',
        ]);
        $reacterable->save();

        $this->assertTrue($reacterable->isRegisteredAsLoveReacter());
        $this->assertInstanceOf(Reacter::class, $reacterable->getLoveReacter());
    }

    /** @test */
    public function it_not_create_new_reacter_if_manually_registered_reacterable_as_reacter_on_create(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new Bot())->getMorphClass(),
        ]);
        $reacterable = new Bot([
            'name' => 'TestBot',
        ]);
        $reacterable->setAttribute('love_reacter_id', $reacter->getId());
        $reacterable->save();

        $this->assertSame(1, Reacter::query()->count());
        $this->assertTrue($reacterable->isRegisteredAsLoveReacter());
        $this->assertInstanceOf(Reacter::class, $reacterable->getLoveReacter());
    }

    /** @test */
    public function it_can_check_if_registered_as_love_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $notRegisteredReacterable = new User();
        $registeredReacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($registeredReacterable->isRegisteredAsLoveReacter());
        $this->assertFalse($notRegisteredReacterable->isRegisteredAsLoveReacter());
    }

    /** @test */
    public function it_can_check_if_not_registered_as_love_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $notRegisteredReacterable = new User();
        $registeredReacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertFalse($registeredReacterable->isNotRegisteredAsLoveReacter());
        $this->assertTrue($notRegisteredReacterable->isNotRegisteredAsLoveReacter());
    }

    /** @test */
    public function it_can_register_as_love_reacter(): void
    {
        Event::fake();
        $user = factory(User::class)->create();

        $user->registerAsLoveReacter();

        $this->assertInstanceOf(Reacter::class, $user->getLoveReacter());
    }

    /** @test */
    public function it_throws_exception_on_register_as_love_reacter_when_already_registered(): void
    {
        $this->expectException(AlreadyRegisteredAsLoveReacter::class);

        $user = factory(User::class)->create();

        $user->registerAsLoveReacter();
    }

    /** @test */
    public function it_can_scope_reacted_to_reactable(): void
    {
        factory(Reacter::class)->create(); // Needed to has not same ids with Reacter
        $reactionType = factory(ReactionType::class)->create();
        $reacterable1 = factory(User::class)->create();
        $reacterable2 = factory(User::class)->create();
        $reacterable3 = factory(User::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);

        $reactedToReactable1 = User::query()
            ->whereReactedTo($reactable1)
            ->get();
        $reactedToReactable2 = User::query()
            ->whereReactedTo($reactable2)
            ->get();

        $this->assertSame([
            $reacterable1->getKey(),
            $reacterable2->getKey(),
            $reacterable3->getKey(),
        ], $reactedToReactable1->pluck('id')->toArray());
        $this->assertSame([
            $reacterable3->getKey(),
        ], $reactedToReactable2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_scope_reacted_to_reactable_and_reaction_type(): void
    {
        factory(Reacter::class)->create(); // Needed to has not same ids with Reacter
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reacterable1 = factory(User::class)->create();
        $reacterable2 = factory(User::class)->create();
        $reacterable3 = factory(User::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
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
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);

        $reactedToReactable1WithReactionType1 = User::query()
            ->whereReactedTo($reactable1, $reactionType1->getName())
            ->get();
        $reactedToReactable1WithReactionType2 = User::query()
            ->whereReactedTo($reactable1, $reactionType2->getName())
            ->get();
        $reactedToReactable2WithReactionType1 = User::query()
            ->whereReactedTo($reactable2, $reactionType1->getName())
            ->get();
        $reactedToReactable2WithReactionType2 = User::query()
            ->whereReactedTo($reactable2, $reactionType2->getName())
            ->get();

        $this->assertSame([
            $reacterable1->getKey(),
            $reacterable3->getKey(),
        ], $reactedToReactable1WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reacterable2->getKey(),
        ], $reactedToReactable1WithReactionType2->pluck('id')->toArray());
        $this->assertSame([
            $reacterable2->getKey(),
            $reacterable3->getKey(),
        ], $reactedToReactable2WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reacterable1->getKey(),
        ], $reactedToReactable2WithReactionType2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_scope_not_reacted_to_reactable(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType = factory(ReactionType::class)->create();
        $reacterable1 = factory(User::class)->create();
        $reacterable2 = factory(User::class)->create();
        $reacterable3 = factory(User::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
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
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);

        $reactedToReactable1 = User::query()
            ->whereNotReactedTo($reactable1)
            ->get();
        $reactedToReactable2 = User::query()
            ->whereNotReactedTo($reactable2)
            ->get();

        $this->assertSame([
            $reactable2->getKey(),
        ], $reactedToReactable1->pluck('id')->toArray());
        $this->assertSame([
            $reactable1->getKey(),
        ], $reactedToReactable2->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_scope_not_reacted_to_reactable_and_reaction_type(): void
    {
        factory(Reactant::class)->create(); // Needed to has not same ids with Reactant
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reacterable1 = factory(User::class)->create();
        $reacterable2 = factory(User::class)->create();
        $reacterable3 = factory(User::class)->create();
        $reactable1 = factory(Article::class)->create();
        $reactable2 = factory(Article::class)->create();
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
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);

        $reactedToReactable1WithReactionType1 = User::query()
            ->whereNotReactedTo($reactable1, $reactionType1->getName())
            ->get();
        $reactedToReactable1WithReactionType2 = User::query()
            ->whereNotReactedTo($reactable1, $reactionType2->getName())
            ->get();
        $reactedToReactable2WithReactionType1 = User::query()
            ->whereNotReactedTo($reactable2, $reactionType1->getName())
            ->get();
        $reactedToReactable2WithReactionType2 = User::query()
            ->whereNotReactedTo($reactable2, $reactionType2->getName())
            ->get();

        $this->assertSame([
            $reacterable2->getKey(),
        ], $reactedToReactable1WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reacterable1->getKey(),
            $reacterable3->getKey(),
        ], $reactedToReactable1WithReactionType2->pluck('id')->toArray());
        $this->assertSame([
            $reacterable1->getKey(),
        ], $reactedToReactable2WithReactionType1->pluck('id')->toArray());
        $this->assertSame([
            $reacterable2->getKey(),
            $reacterable3->getKey(),
        ], $reactedToReactable2WithReactionType2->pluck('id')->toArray());
    }
}
