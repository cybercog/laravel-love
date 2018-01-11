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

namespace Cog\Tests\Laravel\Love\Unit\Like\Models;

use Cog\Laravel\Love\Like\Models\Like;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\TestCase;

/**
 * Class LikeTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Like\Models
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

        $entity->likeBy(1);

        $this->assertInstanceOf(Entity::class, Like::first()->likeable);
    }
}
