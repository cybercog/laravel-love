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

namespace Cog\Tests\Laravel\Love;

use Cog\Laravel\Love\Providers\LoveServiceProvider;
use Cog\Tests\Laravel\Love\Stubs\Models\EntityWithMorphMap;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\File;
use Mockery;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase.
 *
 * @package Cog\Tests\Laravel\Love
 */
abstract class TestCase extends Orchestra
{
    /**
     * Actions to be performed on PHPUnit start.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->destroyPackageMigrations();
        $this->publishPackageMigrations();
        $this->migratePackageTables();
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
            ConsoleServiceProvider::class,
        ];
    }

    /**
     * Publish package migrations.
     *
     * @return void
     */
    protected function publishPackageMigrations(): void
    {
        $this->artisan('vendor:publish', [
            '--force' => '',
            '--tag' => 'migrations',
        ]);
    }

    /**
     * Delete all published package migrations.
     *
     * @return void
     */
    protected function destroyPackageMigrations(): void
    {
        File::cleanDirectory(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/database/migrations');
    }

    /**
     * Perform unit test database migrations.
     *
     * @return void
     */
    protected function migrateUnitTestTables(): void
    {
        $this->loadMigrationsFrom([
            '--realpath' => realpath(__DIR__ . '/database/migrations'),
        ]);
    }

    /**
     * Perform package database migrations.
     *
     * @return void
     */
    protected function migratePackageTables(): void
    {
        $this->loadMigrationsFrom([
            '--realpath' => database_path('migrations'),
        ]);
    }

    /**
     * Register package related model factories.
     *
     * @return void
     */
    protected function registerPackageFactories(): void
    {
        $pathToFactories = realpath(__DIR__ . '/database/factories');
        $this->withFactories($pathToFactories);
    }

    /**
     * Register Morph Mapping for the Eloquent models.
     *
     * @return void
     */
    protected function registerTestMorphMaps(): void
    {
        Relation::morphMap([
            'entity-with-morph-map' => EntityWithMorphMap::class,
        ]);
    }

    /**
     * Register Test User model.
     *
     * @return void
     */
    protected function registerUserModel(): void
    {
        $this->app['config']->set('auth.providers.users.model', User::class);
    }
}
