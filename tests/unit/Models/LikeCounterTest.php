<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Tests\Unit\Models;

use Cog\Likeable\Models\LikeCounter;
use Cog\Likeable\Tests\TestCase;

/**
 * Class LikeCounterTest.
 *
 * @package Cog\Likeable\Tests\Unit\Models
 */
class LikeCounterTest extends TestCase
{
    /** @test */
    public function it_can_fill_count()
    {
        $counter = new LikeCounter([
            'count' => 4,
        ]);

        $this->assertEquals(4, $counter->count);
    }

    /** @test */
    public function it_can_cast_count()
    {
        $like = new LikeCounter([
            'count' => '4',
        ]);

        $this->assertTrue(is_int($like->count));
    }

    /** @test */
    public function it_can_fill_type_id()
    {
        $counter = new LikeCounter([
            'type_id' => 2,
        ]);

        $this->assertEquals(2, $counter->type_id);
    }
}
