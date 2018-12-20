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

namespace Cog\Tests\Laravel\Love\Unit\Reacterable\Models\Traits;

use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class ReacterableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_belong_to_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacterable->reacter->is($reacter));
    }

    /** @test */
    public function it_can_get_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacterable->getReacter()->is($reacter));
    }

    /** @test */
    public function it_can_get_null_reacter(): void
    {
        $reacterable = new User();

        $reacter = $reacterable->getReacter();

        $this->assertInstanceOf(NullReacter::class, $reacter);
    }

    /** @test */
    public function it_register_reacterable_as_reacter_on_create(): void
    {
        $reacterable = new Bot([
            'name' => 'TestBot',
        ]);
        $reacterable->save();

        $this->assertTrue($reacterable->isRegisteredAsReacter());
        $this->assertInstanceOf(Reacter::class, $reacterable->getReacter());
    }

    /** @test */
    public function it_not_create_new_reacter_if_manually_registered_reacterable_as_reacter_on_create(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new Bot())->getMorphClass(),
        ]);
        $reacterable = new Bot([
            'name' => 'TestBot',
        ]);
        $reacterable->setAttribute('love_reacter_id', $reacter->getId());
        $reacterable->save();

        $this->assertSame(1, Reacter::query()->count());
        $this->assertTrue($reacterable->isRegisteredAsReacter());
        $this->assertInstanceOf(Reacter::class, $reacterable->getReacter());
    }

    /** @test */
    public function it_can_determine_if_registered_as_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $notRegisteredReacterable = new User();
        $registeredReacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($registeredReacterable->isRegisteredAsReacter());
        $this->assertFalse($notRegisteredReacterable->isRegisteredAsReacter());
    }

    /** @test */
    public function it_can_determine_if_not_registered_as_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $notRegisteredReacterable = new User();
        $registeredReacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertFalse($registeredReacterable->isNotRegisteredAsReacter());
        $this->assertTrue($notRegisteredReacterable->isNotRegisteredAsReacter());
    }
}
