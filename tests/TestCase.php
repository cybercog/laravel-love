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

use Cog\Laravel\Love\LoveServiceProvider;
use Cog\Tests\Laravel\Love\Stubs\Models\EntityWithMorphMap;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Mockery;
use Orchestra\Database\ConsoleServiceProvider;
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
            ConsoleServiceProvider::class,
        ];
    }

    /**
     * Perform unit test database migrations.
     *
     * @return void
     */
    private function migrateUnitTestTables(): void
    {
        $this->loadMigrationsFrom([
            '--realpath' => realpath(__DIR__ . '/database/migrations'),
        ]);
    }

    /**
     * Register package related model factories.
     *
     * @return void
     */
    private function registerPackageFactories(): void
    {
        $pathToFactories = realpath(__DIR__ . '/database/factories');
        $this->withFactories($pathToFactories);
    }

    /**
     * Register Morph Mapping for the Eloquent models.
     *
     * @return void
     */
    private function registerTestMorphMaps(): void
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
    private function registerUserModel(): void
    {
        $this->app
            ->make('config')
            ->set('auth.providers.users.model', User::class);
    }
}
