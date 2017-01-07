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

use Cog\Likeable\Events\ModelWasDisliked;
use Cog\Likeable\Tests\Stubs\Models\Entity;
use Cog\Likeable\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class ModelWasDislikedTest.
 *
 * @package Cog\Likeable\Tests\Unit\Events
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
