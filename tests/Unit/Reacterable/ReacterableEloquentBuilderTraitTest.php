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

namespace Cog\Tests\Laravel\Love\Unit\Reacterable;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use DateTimeImmutable;

final class ReacterableEloquentBuilderTraitTest extends TestCase
{
    /** @test */
    public function it_can_scope_reacted_to_reactable(): void
    {
        Reacter::factory()->create(); // Needed to have not same ids with Reacter
        $reactionType = ReactionType::factory()->create();
        $reacterable1 = User::factory()->create();
        $reacterable2 = User::factory()->create();
        $reacterable3 = User::factory()->create();
        $reactable1 = Article::factory()->create();
        $reactable2 = Article::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
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
        Reacter::factory()->create(); // Needed to have not same ids with Reacter
        $reactionType1 = ReactionType::factory()->create();
        $reactionType2 = ReactionType::factory()->create();
        $reacterable1 = User::factory()->create();
        $reacterable2 = User::factory()->create();
        $reacterable3 = User::factory()->create();
        $reactable1 = Article::factory()->create();
        $reactable2 = Article::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
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
        Reactant::factory()->create(); // Needed to have not same ids with Reactant
        $reactionType = ReactionType::factory()->create();
        $reacterable1 = User::factory()->create();
        $reacterable2 = User::factory()->create();
        $reacterable3 = User::factory()->create();
        $reactable1 = Article::factory()->create();
        $reactable2 = Article::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
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
        Reactant::factory()->create(); // Needed to have not same ids with Reactant
        $reactionType1 = ReactionType::factory()->create();
        $reactionType2 = ReactionType::factory()->create();
        $reacterable1 = User::factory()->create();
        $reacterable2 = User::factory()->create();
        $reacterable3 = User::factory()->create();
        $reactable1 = Article::factory()->create();
        $reactable2 = Article::factory()->create();
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType2->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable2->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType1->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
        ]);
        Reaction::factory()->create([
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

    /** @test */
    public function it_can_scope_reacted_to_reactable_between_datetime_range(): void
    {
        Reacter::factory()->create(); // Needed to have not same ids with Reacter
        $reactionType = ReactionType::factory()->create();
        $reacterable1 = User::factory()->create();
        $reacterable2 = User::factory()->create();
        $reacterable3 = User::factory()->create();
        $reacterable4 = User::factory()->create();
        $reactable1 = Article::factory()->create();
        $reactedAt1 = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2022-03-27 23:59:59');
        $reactedAt2 = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2022-03-28 00:00:00');
        $reactedAt3 = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2022-03-28 23:59:59');
        $reactedAt4 = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2022-03-29 00:00:00');
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable1->getLoveReacter()->getId(),
            'created_at' => $reactedAt1,
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable2->getLoveReacter()->getId(),
            'created_at' => $reactedAt2,
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable3->getLoveReacter()->getId(),
            'created_at' => $reactedAt3,
        ]);
        Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactable1->getLoveReactant()->getId(),
            'reacter_id' => $reacterable4->getLoveReacter()->getId(),
            'created_at' => $reactedAt4,
        ]);

        $reactedToReactable = User::query()
            ->whereReactedToBetween($reactable1, $reactedAt2, $reactedAt3)
            ->get();

        $this->assertSame([
            $reacterable2->getKey(),
            $reacterable3->getKey(),
        ], $reactedToReactable->pluck('id')->toArray());
    }
}
