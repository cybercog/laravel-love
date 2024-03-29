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

namespace Cog\Tests\Laravel\Love;

use Cog\Laravel\Love\LoveEventServiceProvider;
use Cog\Laravel\Love\LoveServiceProvider;
use Cog\Tests\Laravel\Love\Stubs\Models\MorphMappedReactable;
use Cog\Tests\Laravel\Love\Stubs\Models\MorphMappedReacterable;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Actions to be performed on PHPUnit start.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateUnitTestTables();
        $this->registerPackageFactories();
        $this->registerTestMorphMaps();
        $this->registerUserModel();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * Load package service provider.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            LoveServiceProvider::class,
            LoveEventServiceProvider::class,
        ];
    }

    protected function deletePublishedMigrations(): void
    {
        $file = new Filesystem();
        $file->cleanDirectory(database_path('migrations'));
    }

    /**
     * Perform unit test database migrations.
     *
     * @return void
     */
    private function migrateUnitTestTables(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->artisan('migrate')->run();
    }

    /**
     * Register package related model factories.
     *
     * @return void
     */
    private function registerPackageFactories(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Cog\\Tests\\Laravel\\Love\\Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });
    }

    /**
     * Register Morph Mapping for the Eloquent models.
     *
     * @return void
     */
    private function registerTestMorphMaps(): void
    {
        Relation::morphMap([
            'morph-mapped-reactable' => MorphMappedReactable::class,
            'morph-mapped-reacterable' => MorphMappedReacterable::class,
        ]);
    }

    /**
     * Register Test User model.
     *
     * @return void
     */
    private function registerUserModel(): void
    {
        $this->app
            ->make('config')
            ->set('auth.providers.users.model', User::class);
    }
}
