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

namespace Cog\Tests\Laravel\Love\Unit\Liker\Exceptions;

use Cog\Contracts\Love\Liker\Exceptions\InvalidLiker;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class InvalidLikerTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Liker\Exceptions
 */
class InvalidLikerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_throw_exception_if_not_authenticated_on_like()
    {
        $this->expectException(InvalidLiker::class);

        $entity = factory(Entity::class)->create();

        $entity->like();
    }

    /** @test */
    public function it_can_throw_exception_if_authenticated_but_passed_zero_on_like()
    {
        $this->expectException(InvalidLiker::class);

        $entity = factory(Entity::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->like(0);
    }

    /** @test */
    public function it_can_throw_exception_if_not_authenticated_on_unlike()
    {
        $this->expectException(InvalidLiker::class);

        $entity = factory(Entity::class)->create();

        $entity->unlike();
    }

    /** @test */
    public function it_can_throw_exception_if_authenticated_but_passed_zero_on_unlike()
    {
        $this->expectException(InvalidLiker::class);

        $entity = factory(Entity::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->unlike(0);
    }

    /** @test */
    public function it_can_throw_exception_if_not_authenticated_on_where_liked_by()
    {
        $this->expectException(InvalidLiker::class);

        Entity::whereLikedBy();
    }

    /** @test */
    public function it_can_throw_exception_if_authenticated_but_passed_zero_on_where_liked_by()
    {
        $this->expectException(InvalidLiker::class);

        $user = factory(User::class)->create();
        $this->actingAs($user);

        Entity::whereLikedBy(0);
    }
}
