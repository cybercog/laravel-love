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

use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;
use TypeError;

final class ReactionTest extends TestCase
{
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
    public function it_casts_id_to_string(): void
    {
        $reaction = factory(Reaction::class)->make([
            'id' => 4,
        ]);

        $this->assertSame('4', $reaction->getAttribute('id'));
    }

    /** @test */
    public function it_can_belong_to_type(): void
    {
        $type = factory(ReactionType::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $type->getId(),
        ]);

        $this->assertTrue($reaction->type->is($type));
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reaction->reactant->is($reactant));
    }

    /** @test */
    public function it_can_belong_to_reacter(): void
    {
        $reacter = factory(Reacter::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reaction->reacter->is($reacter));
    }

    /** @test */
    public function it_can_get_id(): void
    {
        $reaction = factory(Reaction::class)->make([
            'id' => '4',
        ]);

        $this->assertSame('4', $reaction->getId());
    }

    /** @test */
    public function it_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reaction = new Reaction();

        $reaction->getId();
    }

    /** @test */
    public function it_can_get_type(): void
    {
        $type = factory(ReactionType::class)->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $type->getId(),
        ]);

        $assertType = $reaction->getType();
        $this->assertTrue($assertType->is($type));
    }

    /** @test */
    public function it_throws_exception_on_get_type_when_type_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reaction = new Reaction();

        $reaction->getType();
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReactant = $reaction->getReactant();
        $this->assertTrue($assertReactant->is($reactant));
    }

    /** @test */
    public function it_throws_exception_on_get_type_when_reactant_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reaction = new Reaction();

        $reaction->getReactant();
    }

    /** @test */
    public function it_can_get_reacter(): void
    {
        $reacter = factory(Reacter::class)->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter->getId(),
        ]);

        $assertReacter = $reaction->getReacter();
        $this->assertTrue($assertReacter->is($reacter));
    }

    /** @test */
    public function it_throws_exception_on_get_type_when_reacter_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reaction = new Reaction();

        $reaction->getReacter();
    }

    /** @test */
    public function it_can_get_weight(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertSame(4, $reaction->getWeight());
    }

    /** @test */
    public function it_can_check_if_reaction_is_of_type(): void
    {
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
        ]);

        $true = $reaction->isOfType($reactionType1);
        $false = $reaction->isOfType($reactionType2);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_check_if_reaction_is_not_of_type(): void
    {
        $reactionType1 = factory(ReactionType::class)->create();
        $reactionType2 = factory(ReactionType::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType1->getId(),
        ]);

        $true = $reaction->isNotOfType($reactionType2);
        $false = $reaction->isNotOfType($reactionType1);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_check_if_reaction_is_to_reactant(): void
    {
        // To skip counters creation
        Event::fake();
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant1->getId(),
        ]);

        $true = $reaction->isToReactant($reactant1);
        $false = $reaction->isToReactant($reactant2);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_check_if_reaction_is_to_reactant_when_reactant_is_null_object(): void
    {
        // To skip counters creation
        Event::fake();
        $reactant = factory(Reactant::class)->create();
        $nullReactant = new NullReactant(new Article());
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $true = $reaction->isToReactant($nullReactant);

        $this->assertFalse($true);
    }

    /** @test */
    public function it_can_check_if_reaction_is_not_to_reactant(): void
    {
        // To skip counters creation
        Event::fake();
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant1->getId(),
        ]);

        $true = $reaction->isNotToReactant($reactant2);
        $false = $reaction->isNotToReactant($reactant1);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_check_if_reaction_is_not_to_reactant_when_reactant_is_null_object(): void
    {
        // To skip counters creation
        Event::fake();
        $reactant = factory(Reactant::class)->create();
        $nullReactant = new NullReactant(new Article());
        $reaction = factory(Reaction::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $true = $reaction->isNotToReactant($nullReactant);

        $this->assertTrue($true);
    }

    /** @test */
    public function it_can_check_if_reaction_is_by_reacter(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter1->getId(),
        ]);

        $true = $reaction->isByReacter($reacter1);
        $false = $reaction->isByReacter($reacter2);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_check_if_reaction_is_by_reacter_when_reacter_is_null_object(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $nullReacter = new NullReacter(new User());
        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter1->getId(),
        ]);

        $true = $reaction->isByReacter($nullReacter);

        $this->assertFalse($true);
    }

    /** @test */
    public function it_can_check_if_reaction_is_not_by_reacter(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter1->getId(),
        ]);

        $true = $reaction->isNotByReacter($reacter2);
        $false = $reaction->isNotByReacter($reacter1);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_check_if_reaction_is_not_by_reacter_when_reacter_is_null_object(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $nullReacter = new NullReacter(new User());
        $reaction = factory(Reaction::class)->create([
            'reacter_id' => $reacter1->getId(),
        ]);

        $true = $reaction->isNotByReacter($nullReacter);

        $this->assertTrue($true);
    }
}
