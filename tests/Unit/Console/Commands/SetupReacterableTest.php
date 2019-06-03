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

namespace Cog\Tests\Laravel\Love\Unit\Console\Commands;

use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

final class SetupReacterableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_only_two_default_types(): void
    {
        $this->markTestIncomplete('TODO');
        $this->disableMocking();
        $status = $this->artisan('love:setup-reacterable', [
            'model' => User::class,
        ]);

        $this->assertSame(0, $status);
    }

    private function disableMocking(): void
    {
        if (!Str::startsWith($this->app->version(), '5.6')) {
            $this->withoutMockingConsoleOutput();
        }
    }
}
