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

namespace Cog\Tests\Laravel\Love\Unit\LikeCounter\Models;

use Cog\Laravel\Love\LikeCounter\Models\LikeCounter;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\TestCase;

/**
 * Class LikeCounterTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\LikeCounter\Models
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
    public function it_can_fill_type_id()
    {
        $counter = new LikeCounter([
            'type_id' => 2,
        ]);

        $this->assertEquals(2, $counter->type_id);
    }

    /** @test */
    public function it_casts_count_to_interger()
    {
        $like = new LikeCounter([
            'count' => '4',
        ]);

        $this->assertTrue(is_int($like->count));
    }

    /** @test */
    public function it_can_belong_to_likeable_model()
    {
        $entity = factory(Entity::class)->create();

        $entity->like(1);

        $this->assertInstanceOf(Entity::class, LikeCounter::first()->likeable);
    }
}
