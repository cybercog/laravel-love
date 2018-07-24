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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionCounter\Models;

use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactionCounterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fill_count(): void
    {
        $counter = new ReactionCounter([
            'count' => 4,
        ]);

        $this->assertSame(4, $counter->getAttribute('count'));
    }

    /** @test */
    public function it_can_fill_type_id(): void
    {
        $counter = new ReactionCounter([
            'reaction_type_id' => 4,
        ]);

        $this->assertSame(4, $counter->getAttribute('reaction_type_id'));
    }

    /** @test */
    public function it_casts_count_to_integer(): void
    {
        $counter = new ReactionCounter([
            'count' => '4',
        ]);

        $this->assertSame(4, $counter->getAttribute('count'));
    }
}
