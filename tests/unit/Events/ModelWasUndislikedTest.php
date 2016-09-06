<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Tests\Unit\Events;

use Cog\Likeable\Events\ModelWasUndisliked;
use Cog\Likeable\Tests\Stubs\Models\Entity;
use Cog\Likeable\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class ModelWasUndislikedTest.
 *
 * @package Cog\Likeable\Tests\Unit\Events
 */
class ModelWasUndislikedTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_fire_model_was_liked_event()
    {
        $this->expectsEvents(ModelWasUndisliked::class);

        $entity = factory(Entity::class)->create();
        $entity->dislike(1);

        $entity->undislike(1);
    }
}
