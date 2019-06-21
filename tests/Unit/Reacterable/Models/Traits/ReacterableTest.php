<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Tests\Laravel\Love\Unit\Reacterable\Models\Traits;

use Cog\Contracts\Love\Reacterable\Exceptions\AlreadyRegisteredAsLoveReacter;
use Cog\Laravel\Love\Reacter\Facades\Reacter as ReacterFacade;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReacterableTest extends TestCase
{
    /** @test */
    public function it_can_belong_to_love_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacterable->loveReacter->is($reacter));
    }

    /** @test */
    public function it_can_get_love_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacterable->getLoveReacter()->is($reacter));
    }

    /** @test */
    public function it_can_get_reacter_null_object_when_reacter_is_null(): void
    {
        $reacterable = new User();

        $reacter = $reacterable->getLoveReacter();

        $this->assertInstanceOf(NullReacter::class, $reacter);
    }

    /** @test */
    public function it_can_convert_to_reacter_facade(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $reacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $reacterFacade = $reacterable->akaLoveReacter();

        $this->assertInstanceOf(ReacterFacade::class, $reacterFacade);
    }

    /** @test */
    public function it_can_convert_to_reacter_facade_when_reacter_is_null(): void
    {
        $reacterable = new User();

        $reacterFacade = $reacterable->akaLoveReacter();

        $this->assertInstanceOf(ReacterFacade::class, $reacterFacade);
    }

    /** @test */
    public function it_register_reacterable_as_reacter_on_create(): void
    {
        $reacterable = new Bot([
            'name' => 'TestBot',
        ]);
        $reacterable->save();

        $this->assertTrue($reacterable->isRegisteredAsLoveReacter());
        $this->assertInstanceOf(Reacter::class, $reacterable->getLoveReacter());
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
        $this->assertTrue($reacterable->isRegisteredAsLoveReacter());
        $this->assertInstanceOf(Reacter::class, $reacterable->getLoveReacter());
    }

    /** @test */
    public function it_can_check_if_registered_as_love_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $notRegisteredReacterable = new User();
        $registeredReacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($registeredReacterable->isRegisteredAsLoveReacter());
        $this->assertFalse($notRegisteredReacterable->isRegisteredAsLoveReacter());
    }

    /** @test */
    public function it_can_check_if_not_registered_as_love_reacter(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $notRegisteredReacterable = new User();
        $registeredReacterable = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertFalse($registeredReacterable->isNotRegisteredAsLoveReacter());
        $this->assertTrue($notRegisteredReacterable->isNotRegisteredAsLoveReacter());
    }

    /** @test */
    public function it_can_register_as_love_reacter(): void
    {
        Event::fake();
        $user = factory(User::class)->create();

        $user->registerAsLoveReacter();

        $this->assertInstanceOf(Reacter::class, $user->getLoveReacter());
    }

    /** @test */
    public function it_throws_exception_on_register_as_love_reacter_when_already_registered(): void
    {
        $this->expectException(AlreadyRegisteredAsLoveReacter::class);

        $user = factory(User::class)->create();

        $user->registerAsLoveReacter();
    }
}
