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

namespace Cog\Tests\Laravel\Love\Unit\Console\Commands;

use Cog\Laravel\Love\Console\Commands\RegisterReactants;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;

final class RegisterReactantsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

//        $this->withoutMockingConsoleOutput();
    }

    /** @test */
    public function it_can_create_reactants_for_all_models(): void
    {
        Article::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $articleReactables = factory(Article::class, 2)->create();
        $userReactables = factory(User::class, 2)->create();
        $command = $this->artisan(RegisterReactants::class, [
            '--model' => Article::class,
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        foreach ($articleReactables as $reactable) {
            $this->assertTrue($reactable->fresh()->isRegisteredAsLoveReactant());
        }
        foreach ($userReactables as $reactable) {
            $this->assertTrue($reactable->fresh()->isNotRegisteredAsLoveReactant());
        }
    }
}
