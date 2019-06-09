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

use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\Person;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Str;

final class SetupReactableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->disableMocking();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->deletePublishedMigrations();
    }

    /** @test */
    public function it_can_create_migration_for_reactable_model(): void
    {
        $status = $this->artisan('love:setup-reactable', [
            'model' => Person::class,
        ]);

        $this->assertSame(0, $status);
        // TODO: Assure that migration file was created
    }

    /** @test */
    public function it_cannot_create_migration_for_reactable_model_when_model_not_exists(): void
    {
        $status = $this->artisan('love:setup-reactable', [
            'model' => 'NotExists',
        ]);

        $this->assertSame(1, $status);
        // TODO: Assure that migration file was not created
    }

    /** @test */
    public function it_cannot_create_migration_for_reactable_model_when_model_not_implements_reactable_contract(): void
    {
        $status = $this->artisan('love:setup-reactable', [
            'model' => Bot::class,
        ]);

        $this->assertSame(1, $status);
        // TODO: Assure that migration file was not created
    }

    /** @test */
    public function it_cannot_create_migration_for_reactable_model_when_column_already_exists(): void
    {
        $status = $this->artisan('love:setup-reactable', [
            'model' => Article::class,
        ]);

        $this->assertSame(1, $status);
        // TODO: Assure that migration file was not created
    }

    private function disableMocking(): void
    {
        if (!Str::startsWith($this->app->version(), '5.6')) {
            $this->withoutMockingConsoleOutput();
        }
    }
}
