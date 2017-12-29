<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Tests\Laravel\Likeable\Unit\Like\Events;

use Cog\Laravel\Likeable\Like\Events\ModelWasDisliked;
use Cog\Tests\Laravel\Likeable\Stubs\Models\Entity;
use Cog\Tests\Laravel\Likeable\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class ModelWasDislikedTest.
 *
 * @package Cog\Tests\Laravel\Likeable\Unit\Like\Events
 */
class ModelWasDislikedTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_fire_model_was_liked_event()
    {
        $this->expectsEvents(ModelWasDisliked::class);

        $entity = factory(Entity::class)->create();
        $entity->dislike(1);
    }
}
