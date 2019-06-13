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

namespace Cog\Laravel\Love\Console\Commands;

use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Support\Database\MigrationCreator;
use Cog\Laravel\Love\Support\Database\AddForeignColumnStub;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class SetupReacterable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'love:setup-reacterable
        {model? : The name of the reacterable model}
        {--nullable : Indicate if foreign column allows null values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up reacterable model';

    private $files;

    private $creator;

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
        $foreignColumn = 'love_reacter_id';
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
                'Model `%s` does not implements Reacterable interface.',
                get_class($model)
            ));

            return 1;
        }

        $table = $model->getTable();
        $referencedModel = new Reacter();
        $referencedTable = $referencedModel->getTable();
        $referencedColumn = $referencedModel->getKeyName();

        if (!Schema::hasTable($referencedTable)) {
            $this->error(sprintf(
                'Referenced table `%s` does not exists in database.',
                $referencedTable
            ));

            return 1;
        }

        if (Schema::hasColumn($table, $foreignColumn)) {
            $this->error(sprintf(
                'Foreign column `%s` already exists in `%s` database table.',
                $foreignColumn,
                $table
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

        $this->composer->dumpAutoloads();

        return 0;
    }

    private function resolveModel(): string
    {
        return $this->argument('model')
            ?? $this->ask('What model should be reacterable?')
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
        return !$model instanceof ReacterableContract;
    }

    private function getMigrationsPath(): string
    {
        return $this->laravel->databasePath('migrations');
    }
}
