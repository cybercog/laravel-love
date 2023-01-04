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

namespace Cog\Laravel\Love\Support\Database;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

final class MigrationCreator
{
    private Filesystem $files;

    public function __construct(
        Filesystem $files,
    ) {
        $this->files = $files;
    }

    /**
     * Create a new migration at the given path.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function create(
        string $basePath,
        AddForeignColumnStub $migrationStub,
    ): void {
        $this->ensureMigrationDoesntAlreadyExist($migrationStub->getClass());

        $this->files->put(
            $this->getPath($basePath, $migrationStub->getFilename()),
            $migrationStub->getPopulatedContent()
        );
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
     * @throws \InvalidArgumentException
     */
    private function ensureMigrationDoesntAlreadyExist(
        string $className,
    ): void {
        if (class_exists($className)) {
            // TODO: (?) Throw custom exception?
            throw new InvalidArgumentException("Migration class `{$className}` already exists.");
        }
    }

    /**
     * Get the full path to the migration.
     */
    private function getPath(
        string $basePath,
        string $name,
    ): string {
        return sprintf('%s/%s_%s.php', $basePath, $this->getDatePrefix(), $name);
    }

    /**
     * Get the date prefix for the migration.
     */
    private function getDatePrefix(): string
    {
        return date('Y_m_d_His');
    }
}
