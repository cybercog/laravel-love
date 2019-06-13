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

use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\Person;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
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
            '--model' => Person::class,
        ]);

        $this->assertSame(0, $status);
        $this->assertTrue($this->isMigrationFileExists('add_love_reactant_id_to_people_table'));
        // Can't check if column was created because of SQLite bug:
        // Cannot add a NOT NULL column with default value NULL
    }

    /** @test */
    public function it_can_create_migration_for_reactable_model_with_nullable_column(): void
    {
        $status = $this->artisan('love:setup-reactable', [
            '--model' => Person::class,
            '--nullable' => true,
        ]);

        $this->assertSame(0, $status);
        $this->assertTrue($this->isMigrationFileExists('add_love_reactant_id_to_people_table'));
        $this->artisan('migrate');
        $this->assertTrue(Schema::hasColumn('people', 'love_reactant_id'));
    }

    /** @test */
    public function it_cannot_create_migration_for_reactable_model_when_model_not_exists(): void
    {
        $status = $this->artisan('love:setup-reactable', [
            '--model' => 'NotExists',
        ]);

        $this->assertSame(1, $status);
        $this->assertFalse($this->isMigrationFileExists('add_love_reactant_id_to_not_exists_table'));
    }

    /** @test */
    public function it_cannot_create_migration_for_reactable_model_when_model_not_implements_reactable_contract(): void
    {
        $status = $this->artisan('love:setup-reactable', [
            '--model' => Bot::class,
        ]);

        $this->assertSame(1, $status);
        $this->assertFalse($this->isMigrationFileExists('add_love_reactant_id_to_bots_table'));
    }

    /** @test */
    public function it_cannot_create_migration_for_reactable_model_when_reactants_table_not_exists(): void
    {
        Schema::drop('love_reactants');
        $status = $this->artisan('love:setup-reactable', [
            '--model' => Person::class,
        ]);

        $this->assertSame(1, $status);
        $this->assertFalse($this->isMigrationFileExists('add_love_reactant_id_to_people_table'));
    }

    /** @test */
    public function it_cannot_create_migration_for_reactable_model_when_column_already_exists(): void
    {
        $status = $this->artisan('love:setup-reactable', [
            '--model' => Article::class,
        ]);

        $this->assertSame(1, $status);
        $this->assertFalse(class_exists('AddLoveReactantIdToArticlesTable'));
        $this->assertFalse($this->isMigrationFileExists('add_love_reactant_id_to_articles_table'));
    }

    private function disableMocking(): void
    {
        if (!Str::startsWith($this->app->version(), '5.6')) {
            $this->withoutMockingConsoleOutput();
        }
    }

    private function isMigrationFileExists(string $filename): bool
    {
        $files = File::files(database_path('migrations'));
        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            if (Str::contains($file->getFilename(), $filename)) {
                return true;
            }
        }

        return false;
    }
}
