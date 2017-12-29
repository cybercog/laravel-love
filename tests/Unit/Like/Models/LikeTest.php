<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Tests\Laravel\Likeable\Unit\Like\Models;

use Cog\Laravel\Likeable\Like\Models\Like;
use Cog\Tests\Laravel\Likeable\Stubs\Models\Entity;
use Cog\Tests\Laravel\Likeable\TestCase;

/**
 * Class LikeTest.
 *
 * @package Cog\Tests\Laravel\Likeable\Unit\Like\Models
 */
class LikeTest extends TestCase
{
    /** @test */
    public function it_can_fill_user_id()
    {
        $like = new Like([
            'user_id' => 4,
        ]);

        $this->assertEquals(4, $like->user_id);
    }

    /** @test */
    public function it_can_fill_type_id()
    {
        $like = new Like([
            'type_id' => 2,
        ]);

        $this->assertEquals(2, $like->type_id);
    }

    /** @test */
    public function it_can_belong_to_likeable_model()
    {
        $entity = factory(Entity::class)->create();

        $entity->like(1);

        $this->assertInstanceOf(Entity::class, Like::first()->likeable);
    }
}
