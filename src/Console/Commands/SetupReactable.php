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

namespace Cog\Laravel\Love\Console\Commands;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Support\Database\AddForeignColumnStub;
use Cog\Laravel\Love\Support\Database\MigrationCreator;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'love:setup-reactable', description: 'Set up reactable model')]
final class SetupReactable extends Command
{
    private Filesystem $files;

    private MigrationCreator $creator;

    private Composer $composer;

    public function handle(
        Filesystem $files,
        MigrationCreator $creator,
        Composer $composer,
    ): int {
        $this->files = $files;
        $this->creator = $creator;
        $this->composer = $composer;

        $model = $this->resolveModel();
        $model = $this->sanitizeName($model);
        $foreignColumn = 'love_reactant_id';
        $isForeignColumnNullable = boolval($this->option('not-nullable')) === false;

        if (!class_exists($model)) {
            $this->error(
                sprintf(
                    'Model `%s` not exists.',
                    $model
                )
            );

            return self::FAILURE;
        }

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $model();

        if ($this->isModelInvalid($model)) {
            $this->error(
                sprintf(
                    'Model `%s` does not implements Reactable interface.',
                    get_class($model)
                )
            );

            return self::FAILURE;
        }

        $table = $model->getTable();
        $referencedModel = new Reactant();
        $referencedSchema = Schema::connection($referencedModel->getConnectionName());
        $referencedTable = $referencedModel->getTable();
        $referencedColumn = $referencedModel->getKeyName();

        if (!$referencedSchema->hasTable($referencedTable)) {
            $this->error(
                sprintf(
                    'Referenced table `%s` does not exists in database.',
                    $referencedTable
                )
            );

            return self::FAILURE;
        }

        if (Schema::hasColumn($table, $foreignColumn)) {
            $this->error(
                sprintf(
                    'Foreign column `%s` already exists in `%s` database table.',
                    $foreignColumn,
                    $table
                )
            );

            return self::FAILURE;
        }

        try {
            $stub = new AddForeignColumnStub(
                $this->files,
                $table,
                $referencedTable,
                $foreignColumn,
                $referencedColumn,
                $isForeignColumnNullable
            );

            $this->creator->create($this->getMigrationsPath(), $stub);
        } catch (FileNotFoundException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Migration created successfully!');

        $this->composer->dumpAutoloads();

        return self::SUCCESS;
    }

    /**
     * @return array<int, InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'model',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The name of the reactable model',
            ),
            new InputOption(
                name: 'not-nullable',
                mode: InputOption::VALUE_NONE,
                description: 'Indicate if foreign column does not allow null values',
            ),
        ];
    }

    private function resolveModel(): string
    {
        return $this->option('model')
            ?? $this->ask('What model should be reactable?')
            ?? $this->resolveModel();
    }

    private function sanitizeName(
        string $name,
    ): string {
        $name = trim($name);
        $name = Str::studly($name);

        return $name;
    }

    private function isModelInvalid(
        Model $model,
    ): bool {
        return !$model instanceof ReactableInterface;
    }

    private function getMigrationsPath(): string
    {
        return $this->laravel->databasePath('migrations');
    }
}
