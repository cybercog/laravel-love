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

use Cog\Laravel\Love\Likeable\Events\ModelWasLiked;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class ModelWasLikedTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Likeable\Events
 */
class ModelWasLikedTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_fire_model_was_liked_event()
    {
        $this->expectsEvents(ModelWasLiked::class);

        $entity = factory(Entity::class)->create();
        $entity->like(1);
    }
}
