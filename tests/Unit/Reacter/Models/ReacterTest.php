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

namespace Cog\Tests\Laravel\Love\Unit\Reacter\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReacterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_morph_to_reacterable(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getKey(),
        ]);

        $this->assertTrue($reacter->reacterable->is($reacterable));
    }

    /** @test */
    public function it_can_has_reaction(): void
    {
        $reacter = factory(Reacter::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter->getKey(),
        ]);

        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    /** @test */
    public function it_can_has_many_reactions(): void
    {
        $reacter = factory(Reacter::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reacter_id' => $reacter->getKey(),
        ]);

        $assertReactions = $reacter->reactions;
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_react_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->reactant;

        $reacter->reactTo($reactant, $reactionType);

        $this->assertCount(1, $reacter->reactions);
        $assertReaction = $reacter->reactions->first();
        $this->assertTrue($assertReaction->reactant->is($reactant));
    }

    /** @test */
    public function it_can_unreact_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reactant_id' => $reactant->getKey(),
            'reacter_id' => $reacter->getKey(),
        ]);

        $reacter->unreactTo($reactant, $reactionType);

        $this->assertCount(0, $reacter->reactions);
        $this->assertFalse($reaction->exists());
    }

    /** @test */
    public function it_cannot_duplicate_reactions(): void
    {
        $this->expectException(\RuntimeException::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reacter->reactTo($reactant, $reactionType);
        $reacter->reactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_cannot_unreact_reactant_if_not_reacted(): void
    {
        $this->expectException(\RuntimeException::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reacter_id' => $reacter->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType,
            'reactant_id' => $reactant->getKey(),
        ]);

        $reacter->unreactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_can_check_is_reacted_to_reactant(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = $reacter->isReactedTo($reactant);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_reactant(): void
    {
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reacter_id' => $reacter->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $isNotReacted = $reacter->isNotReactedTo($reactant);

        $this->assertTrue($isNotReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_with_type_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isReacted = $reacter->isReactedWithTypeTo($reactant, $reactionType);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_with_type_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reacter = factory(Reacter::class)->create();
        $reactant = factory(Reactant::class)->create();
        factory(Reaction::class)->create([
            'reacter_id' => $reacter->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);
        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $isNotReacted = $reacter->isNotReactedWithTypeTo($reactant, $reactionType);

        $this->assertTrue($isNotReacted);
    }
}
