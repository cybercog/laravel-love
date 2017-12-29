<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Tests\Unit\Events;

use Cog\Likeable\Events\ModelWasUnliked;
use Cog\Tests\Laravel\Likeable\Stubs\Models\Entity;
use Cog\Tests\Laravel\Likeable\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class ModelWasUnlikedTest.
 *
 * @package Cog\Tests\Laravel\Likeable\Unit\Events
 */
class ModelWasUnlikedTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_fire_model_was_liked_event()
    {
        $this->expectsEvents(ModelWasUnliked::class);

        $entity = factory(Entity::class)->create();
        $entity->like(1);

        $entity->unlike(1);
    }
}
