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

namespace Cog\Tests\Laravel\Love\Unit\ReactionType\Models;

use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactionTypeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_fill_name(): void
    {
        $type = new ReactionType([
            'name' => 'TestType',
        ]);

        $this->assertSame('TestType', $type->getAttribute('name'));
    }

    /** @test */
    public function it_can_fill_weight(): void
    {
        $type = new ReactionType([
            'weight' => 4,
        ]);

        $this->assertSame(4, $type->getAttribute('weight'));
    }

    /** @test */
    public function it_casts_weight_to_integer(): void
    {
        $type = new ReactionType([
            'weight' => '4',
        ]);

        $this->assertSame(4, $type->getAttribute('weight'));
    }

    /** @test */
    public function it_can_has_reaction(): void
    {
        $type = factory(ReactionType::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $type->getKey(),
        ]);

        $assertReaction = $type->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    /** @test */
    public function it_can_has_many_reactions(): void
    {
        $type = factory(ReactionType::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reaction_type_id' => $type->getKey(),
        ]);

        $assertReactions = $type->reactions;
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_instantiate_reaction_type_from_name(): void
    {
        $type = factory(ReactionType::class)->create([
            'name' => 'TestType',
        ]);

        $assertType = ReactionType::fromName('TestType');

        $this->assertTrue($assertType->is($type));
    }

    /** @test */
    public function it_throws_exception_if_instantiate_reaction_type_from_not_exist_name(): void
    {
        $this->expectException(\RuntimeException::class);

        factory(ReactionType::class)->create();

        ReactionType::fromName('NotExistType');
    }
}
