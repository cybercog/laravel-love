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

namespace Cog\Tests\Laravel\Love\Unit\Likeable\Events;

use Cog\Laravel\Love\Likeable\Events\LikeableWasDisliked;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class LikeableWasDislikedTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Likeable\Events
 */
class LikeableWasDislikedTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_fire_model_was_liked_event()
    {
        $this->expectsEvents(LikeableWasDisliked::class);

        $entity = factory(Entity::class)->create();
        $entity->dislike(1);
    }
}
