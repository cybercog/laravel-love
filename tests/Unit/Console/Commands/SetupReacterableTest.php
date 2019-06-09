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

use Cog\Tests\Laravel\Love\Stubs\Models\Person;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

final class SetupReacterableTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        $path = realpath(base_path('../laravel/database/migrations'));
        $file = new Filesystem();
        $file->cleanDirectory($path);
    }

    /** @test */
    public function it_can_create_migration_for_reacterable_model_when_column_not_exists(): void
    {
        $this->disableMocking();
        $status = $this->artisan('love:setup-reacterable', [
            'model' => Person::class,
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
