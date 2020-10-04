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
use Symfony\Component\Console\Input\InputOption;

final class SetupReactable extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'love:setup-reactable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up reactable model';

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * @var MigrationCreator
     */
    private $creator;

    /**
     * @var Composer
     */
    private $composer;

    public function __construct(Filesystem $files, MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->creator = $creator;
        $this->composer = $composer;
    }

    public function handle(): int
    {
        $model = $this->resolveModel();
        $model = $this->sanitizeName($model);
        $foreignColumn = 'love_reactant_id';
        $isForeignColumnNullable = boolval($this->option('nullable'));

        if (!class_exists($model)) {
            $this->error(sprintf(
                'Model `%s` not exists.',
                $model
            ));

            return 1;
        }

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $model();

        if ($this->isModelInvalid($model)) {
            $this->error(sprintf(
                'Model `%s` does not implements Reactable interface.',
                get_class($model)
            ));

            return 1;
        }

        $table = $model->getTable();
        $referencedModel = new Reactant();
        $referencedSchema = Schema::connection($referencedModel->getConnectionName());
        $referencedTable = $referencedModel->getTable();
        $referencedColumn = $referencedModel->getKeyName();

        if (!$referencedSchema->hasTable($referencedTable)) {
            $this->error(sprintf(
                'Referenced table `%s` does not exists in database.',
                $referencedTable
            ));

            return 1;
        }

        if (Schema::hasColumn($table, $foreignColumn)) {
            $this->error(sprintf(
                'Foreign column `%s` already exists in `%s` database table.',
                $foreignColumn, $table
            ));

            return 1;
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

            return 1;
        }

        $this->info('Migration created successfully!');

        $this->composer->dumpAutoloads();

        return 0;
    }

    /**
     * @return array[]
     */
    protected function getOptions(): array
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'The name of the reactable model'],
            ['nullable', null, InputOption::VALUE_NONE, 'Indicate if foreign column allows null values'],
        ];
    }

    private function resolveModel(): string
    {
        return $this->option('model')
            ?? $this->ask('What model should be reactable?')
            ?? $this->resolveModel();
    }

    private function sanitizeName(string $name): string
    {
        $name = trim($name);
        $name = Str::studly($name);

        return $name;
    }

    private function isModelInvalid(Model $model): bool
    {
        return !$model instanceof ReactableInterface;
    }

    private function getMigrationsPath(): string
    {
        return $this->laravel->databasePath('migrations');
    }
}
