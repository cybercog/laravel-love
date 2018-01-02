<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Tests\Laravel\Likeable\Unit\Liker\Exceptions;

use \Cog\Contracts\Likeable\Liker\Exceptions\LikerNotDefined;
use Cog\Tests\Laravel\Likeable\Stubs\Models\Entity;
use Cog\Tests\Laravel\Likeable\Stubs\Models\User;
use Cog\Tests\Laravel\Likeable\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class LikerNotDefinedTest.
 *
 * @package Cog\Tests\Laravel\Likeable\Unit\Liker\Exceptions
 */
class LikerNotDefinedTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_throw_exception_if_not_authenticated_on_like()
    {
        $this->expectException(LikerNotDefined::class);

        $entity = factory(Entity::class)->create();

        $entity->like();
    }

    /** @test */
    public function it_can_throw_exception_if_authenticated_but_passed_zero_on_like()
    {
        $this->expectException(LikerNotDefined::class);

        $entity = factory(Entity::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->like(0);
    }

    /** @test */
    public function it_can_throw_exception_if_not_authenticated_on_unlike()
    {
        $this->expectException(LikerNotDefined::class);

        $entity = factory(Entity::class)->create();

        $entity->unlike();
    }

    /** @test */
    public function it_can_throw_exception_if_authenticated_but_passed_zero_on_unlike()
    {
        $this->expectException(LikerNotDefined::class);

        $entity = factory(Entity::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->unlike(0);
    }

    /** @test */
    public function it_can_throw_exception_if_not_authenticated_on_where_liked_by()
    {
        $this->expectException(LikerNotDefined::class);

        Entity::whereLikedBy();
    }

    /** @test */
    public function it_can_throw_exception_if_authenticated_but_passed_zero_on_where_liked_by()
    {
        $this->expectException(LikerNotDefined::class);

        $user = factory(User::class)->create();
        $this->actingAs($user);

        Entity::whereLikedBy(0);
    }
}
