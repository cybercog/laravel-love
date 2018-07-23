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
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_fill_reactant_id(): void
    {
        $reaction = new Reaction([
            'reactant_id' => 4,
        ]);

        $this->assertSame(4, $reaction->getAttribute('reactant_id'));
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $reaction = factory(Reaction::class)->create([
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
}
