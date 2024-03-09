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

namespace Cog\Tests\Laravel\Love\Unit\Reaction\Models;

use Cog\Contracts\Love\Reaction\Exceptions\RateInvalid;
use Cog\Contracts\Love\Reaction\Exceptions\RateOutOfRange;
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
    public function it_can_fill_reactant_id(): void
    {
        $reaction = new Reaction([
            'reactant_id' => 4,
        ]);

        $this->assertSame(4, $reaction->getAttribute('reactant_id'));
    }

    /** @test */
    public function it_can_fill_reaction_type_id(): void
    {
        $reaction = new Reaction([
            'reaction_type_id' => 4,
        ]);

        $this->assertSame(4, $reaction->getAttribute('reaction_type_id'));
    }

    /** @test */
    public function it_can_fill_rate(): void
    {
        $reaction = new Reaction([
            'rate' => 4.0,
        ]);

        $this->assertSame(4.0, $reaction->getRate());
    }

    /** @test */
    public function it_throws_rate_out_of_range_on_fill_rate_with_underflow_value(): void
    {
        $this->expectException(RateOutOfRange::class);

        new Reaction([
            'rate' => Reaction::RATE_MIN - 0.01,
        ]);
    }

    /** @test */
    public function it_throws_rate_out_of_range_on_fill_rate_with_overflow_value(): void
    {
        $this->expectException(RateOutOfRange::class);

        new Reaction([
            'rate' => Reaction::RATE_MAX + 0.01,
        ]);
    }

    /** @test */
    public function it_casts_id_to_string(): void
    {
        $reaction = new Reaction([
            'id' => 4,
        ]);

        $this->assertSame('4', $reaction->getId());
    }

    /** @test */
    public function it_casts_int_rate_to_float(): void
    {
        $reaction = new Reaction([
            'rate' => '4',
        ]);

        $this->assertSame(4.0, $reaction->getRate());
    }

    /** @test */
    public function it_casts_string_rate_to_float(): void
    {
        $reaction1 = new Reaction([
            'rate' => '4',
        ]);
        $reaction2 = new Reaction([
            'rate' => '4.1',
        ]);

        $this->assertSame(4.0, $reaction1->getRate());
        $this->assertSame(4.1, $reaction2->getRate());
    }

    /** @test */
    public function it_casts_null_rate_to_default_value(): void
    {
        $reaction = new Reaction([
            'rate' => null,
        ]);

        $this->assertSame(Reaction::RATE_DEFAULT, $reaction->getRate());
    }

    /** @test */
    public function it_casts_not_set_rate_to_default_value(): void
    {
        $reaction = new Reaction();

        $this->assertSame(Reaction::RATE_DEFAULT, $reaction->getRate());
    }

    /** @test */
    public function it_can_create_reaction_with_default_value(): void
    {
        $reaction1 = Reaction::factory()->create();
        $reaction2 = Reaction::factory()->create([
            'rate' => null,
        ]);

        $this->assertSame(Reaction::RATE_DEFAULT, $reaction1->getRate());
        $this->assertSame(Reaction::RATE_DEFAULT, $reaction2->getRate());
    }

    /** @test */
    public function it_can_belong_to_type(): void
    {
        $type = ReactionType::factory()->create();

        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $type->getId(),
        ]);

        $this->assertTrue($reaction->type->is($type));
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reactant = Reactant::factory()->create();

        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reaction->reactant->is($reactant));
    }

    /** @test */
    public function it_can_belong_to_reacter(): void
    {
        $reacter = Reacter::factory()->create();

        $reaction = Reaction::factory()->create([
            'reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reaction->reacter->is($reacter));
    }

    /** @test */
    public function it_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reaction = new Reaction();

        $reaction->getId();
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactionType = ReactionType::factory()->create();
        $reactant = Reactant::factory()->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $assertReactant = $reaction->getReactant();
        $this->assertTrue($reactant->is($assertReactant));
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
        $reacter = Reacter::factory()->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = Reaction::factory()->create([
            'reacter_id' => $reacter->getId(),
        ]);

        $assertReacter = $reaction->getReacter();
        $this->assertTrue($reacter->is($assertReacter));
    }

    /** @test */
    public function it_throws_exception_on_get_type_when_reacter_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reaction = new Reaction();

        $reaction->getReacter();
    }

    /** @test */
    public function it_can_get_type(): void
    {
        $type = ReactionType::factory()->create();

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $type->getId(),
        ]);

        $assertType = $reaction->getType();
        $this->assertTrue($type->is($assertType));
    }

    /** @test */
    public function it_throws_exception_on_get_type_when_type_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reaction = new Reaction();

        $reaction->getType();
    }

    /** @test */
    public function it_can_get_weight(): void
    {
        $reactionType = ReactionType::factory()->create([
            'mass' => 4,
        ]);

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertSame(4.0, $reaction->getWeight());
    }

    /** @test */
    public function it_can_get_weight_affected_by_rate(): void
    {
        $reactionType = ReactionType::factory()->create([
            'mass' => 4,
        ]);

        /** @var \Cog\Laravel\Love\Reaction\Models\Reaction $reaction */
        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
            'rate' => 1.02,
        ]);

        $this->assertSame(4.08, $reaction->getWeight());
    }

    /** @test */
    public function it_can_check_if_reaction_is_of_type(): void
    {
        $reactionType1 = ReactionType::factory()->create();
        $reactionType2 = ReactionType::factory()->create();
        $reaction = Reaction::factory()->create([
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
        $reactionType1 = ReactionType::factory()->create();
        $reactionType2 = ReactionType::factory()->create();
        $reaction = Reaction::factory()->create([
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
        $reactant1 = Reactant::factory()->create();
        $reactant2 = Reactant::factory()->create();
        $reaction = Reaction::factory()->create([
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
        $reactant = Reactant::factory()->create();
        $nullReactant = new NullReactant(new Article());
        $reaction = Reaction::factory()->create([
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
        $reactant1 = Reactant::factory()->create();
        $reactant2 = Reactant::factory()->create();
        $reaction = Reaction::factory()->create([
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
        $reactant = Reactant::factory()->create();
        $nullReactant = new NullReactant(new Article());
        $reaction = Reaction::factory()->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $true = $reaction->isNotToReactant($nullReactant);

        $this->assertTrue($true);
    }

    /** @test */
    public function it_can_check_if_reaction_is_by_reacter(): void
    {
        $reacter1 = Reacter::factory()->create();
        $reacter2 = Reacter::factory()->create();
        $reaction = Reaction::factory()->create([
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
        $reacter1 = Reacter::factory()->create();
        $nullReacter = new NullReacter(new User());
        $reaction = Reaction::factory()->create([
            'reacter_id' => $reacter1->getId(),
        ]);

        $true = $reaction->isByReacter($nullReacter);

        $this->assertFalse($true);
    }

    /** @test */
    public function it_can_check_if_reaction_is_not_by_reacter(): void
    {
        $reacter1 = Reacter::factory()->create();
        $reacter2 = Reacter::factory()->create();
        $reaction = Reaction::factory()->create([
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
        $reacter1 = Reacter::factory()->create();
        $nullReacter = new NullReacter(new User());
        $reaction = Reaction::factory()->create([
            'reacter_id' => $reacter1->getId(),
        ]);

        $true = $reaction->isNotByReacter($nullReacter);

        $this->assertTrue($true);
    }

    /** @test */
    public function it_can_change_rate(): void
    {
        $reaction = Reaction::factory()->create([
            'rate' => 1.0,
        ]);

        $reaction->changeRate(2.0);

        $this->assertSame(2.0, $reaction->rate);
    }

    /** @test */
    public function it_throws_rate_out_of_range_on_change_rate_with_overflow_value(): void
    {
        $this->expectException(RateOutOfRange::class);

        $reaction = Reaction::factory()->create([
            'rate' => 1.0,
        ]);

        $reaction->changeRate(Reaction::RATE_MAX + 0.01);
    }

    /** @test */
    public function it_throws_rate_out_of_range_on_change_rate_with_underflow_value(): void
    {
        $this->expectException(RateOutOfRange::class);

        $reaction = Reaction::factory()->create([
            'rate' => 1.0,
        ]);

        $reaction->changeRate(Reaction::RATE_MIN - 0.01);
    }

    /** @test */
    public function it_throws_rate_invalid_on_change_rate_with_same_value(): void
    {
        $this->expectException(RateInvalid::class);

        $reaction = Reaction::factory()->create([
            'rate' => 1.0,
        ]);

        $reaction->changeRate(1.0);
    }
}
