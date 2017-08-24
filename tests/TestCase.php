<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Tests;

use Cog\Likeable\Tests\Stubs\Models\EntityWithMorphMap;
use Cog\Likeable\Tests\Stubs\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\File;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase.
 *
 * @package Cog\Likeable\Tests
 */
abstract class TestCase extends Orchestra
{
    /**
     * Actions to be performed on PHPUnit start.
     */
    protected function setUp()
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

    public function tearDown()
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
    protected function getPackageProviders($app)
    {
        return [
            \Cog\Likeable\Providers\LikeableServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
        ];
    }

    /**
     * Publish package migrations.
     */
    protected function publishPackageMigrations()
    {
        $this->artisan('vendor:publish', ['--force' => '']);
    }

    /**
     * Delete all published package migrations.
     */
    protected function destroyPackageMigrations()
    {
        File::cleanDirectory('vendor/orchestra/testbench-core/fixture/database/migrations');
    }

    /**
     * Perform unit test database migrations.
     */
    protected function migrateUnitTestTables()
    {
        $this->loadMigrationsFrom([
            //'--database' => 'sqlite',
            '--realpath' => realpath(__DIR__ . '/database/migrations'),
        ]);
    }

    /**
     * Perform package database migrations.
     */
    protected function migratePackageTables()
    {
        $this->loadMigrationsFrom([
            //'--database' => 'sqlite',
            '--realpath' => database_path('migrations'),
        ]);
    }

    /**
     * Register package related model factories.
     */
    protected function registerPackageFactories()
    {
        $pathToFactories = realpath(__DIR__ . '/database/factories');
        $this->withFactories($pathToFactories);
    }

    protected function registerTestMorphMaps()
    {
        Relation::morphMap([
            'entity-with-morph-map' => EntityWithMorphMap::class,
        ]);
    }

    protected function registerUserModel()
    {
        $this->app['config']->set('auth.providers.users.model', User::class);
    }
}
