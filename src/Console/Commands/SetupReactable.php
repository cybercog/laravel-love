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

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class SetupReactable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'love:setup-reactable
        {model? : The name of the reactable model}
        {--nullable : Indicate if foreign column should be created as nullable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up reactable model';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new migration install command instance.
     *
     * @param \Illuminate\Database\Migrations\MigrationCreator $creator
     * @param \Illuminate\Support\Composer $composer
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $model = $this->resolveModel();
        $model = $this->sanitizeName($model);

        if (!class_exists($model)) {
            $this->error(sprintf(
                'Class `%s` not exists.',
                $model
            ));

            return 1;
        }

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $model();

        if ($this->isModelInvalid($model)) {
            $this->error(sprintf(
                'Reaction type with name `%s` is invalid.',
                $model
            ));

            return 1;
        }

        if (Schema::hasColumn($model->getTable(), 'love_reactant_id')) {
            $this->error(sprintf(
                'Column `love_reactant_id` already exists in `%s` database table.',
                $model->getTable()
            ));

            return 1;
        }

        $this->createMigrationForModel($model);

        $this->composer->dumpAutoloads();

        return 0;
    }

    private function resolveModel(): string
    {
        return $this->argument('model')
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
        return !$model instanceof ReactableContract;
    }

    private function createMigrationForModel(Model $model): void
    {
        $migrationStubPath = __DIR__ . '/../../../database/migrationStubs/AddForeignColumn.php';
        $migrationStub = File::get($migrationStubPath);
        $table = $model->getTable();
        $column = 'love_reactant_id';
        $foreignTable = 'love_reactants';
        $filename = sprintf('add_%s_to_%s_table', $column, $table);
        $className = Str::studly($filename);
        $timestamp = Carbon::now()->format('Y_m_d_His');

        $migrationStub = str_replace('AddForeignColumn', $className, $migrationStub);
        $migrationStub = str_replace('{table}', $table, $migrationStub);
        $migrationStub = str_replace('{column}', $column, $migrationStub);
        $migrationStub = str_replace('{foreignTable}', $foreignTable, $migrationStub);

        File::put(database_path("migrations/{$timestamp}_{$filename}.php"), $migrationStub);
    }
}
