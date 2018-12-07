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

namespace Cog\Tests\Laravel\Love\Unit\Reaction\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class ReactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fill_reaction_type_id(): void
    {
        $reaction = new Reaction([
            'reaction_type_id' => 4,
        ]);

        $this->assertSame(4, $reaction->getAttribute('reaction_type_id'));
    }

    /** @test */
    public function it_can_fill_reactant_id(): void
    {
        $reaction = new Reaction([
            'reactant_id' => 4,
        ]);

        $this->assertSame(4, $reaction->getAttribute('reactant_id'));
    }

    /** @test */
    public function it_can_belong_to_type(): void
    {
        $type = factory(ReactionType::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $type->getKey(),
        ]);

        $this->assertTrue($reaction->type->is($type));
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reaction->reactant->is($reactant));
    }

    /** @test */
    public function it_can_belong_to_reacter(): void
    {
        $reacter = factory(Reacter::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter->getKey(),
        ]);

        $this->assertTrue($reaction->reacter->is($reacter));
    }

    /** @test */
    public function it_can_get_type(): void
    {
        $type = factory(ReactionType::class)->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $type->getKey(),
        ]);

        $assertType = $reaction->getType();
        $this->assertTrue($assertType->is($type));
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $assertReactant = $reaction->getReactant();
        $this->assertTrue($assertReactant->is($reactant));
    }

    /** @test */
    public function it_can_get_reacter(): void
    {
        $reacter = factory(Reacter::class)->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter->getKey(),
        ]);

        $assertReacter = $reaction->getReacter();
        $this->assertTrue($assertReacter->is($reacter));
    }

    /** @test */
    public function it_can_get_weight(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
        ]);

        $this->assertSame(4, $reaction->getWeight());
    }
}
