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

use Illuminate\Console\Command;

class RegisterExistingReactables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'love:register-reactables
														{ modelName : Namespace of target model (e.g. "App\\\\Comment")}
														{ --ids= : Comma-separated list of model IDs, or omit this argument for all IDs (e.g. "1,2,16,34")}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers any existing unregistered reactables (Models)';

    protected $modelIds;

    protected $modelName;

    protected $modelPrimaryKeyName;

    protected $modelsRegistered;

    protected $modelsAlreadyRegistered;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->modelName = $this->argument('modelName');
        $this->modelIds = $this->option('ids');

        $this->line("\n" . '<fg=yellow;options=underscore>Registering Reactants ...</>' . "\n");
        $this->line('       Target model: <fg=Cyan>' . $this->modelName . '</>');

        // Verify that the Model class actually exists
        if (!class_exists($this->modelName)) {
            $this->line('Model class exists?: <fg=red;options=bold>No</>');
            $errorMessage = 'Model not found! Check your spelling, and be sure to escape any namespace backslashes.';
            $this->line("\n" . '              <fg=red;options=bold>Error:</> <fg=red>' . $errorMessage . '</>' . "\n");

            return 1;
        }

        $this->line('Model class exists?: <fg=green>Yes</>');

        // Determine the primary key of the target model
        $this->modelPrimaryKeyName = (new $this->modelName)->getKeyName();
        $this->line('   Primary Key Name: <fg=Cyan>' . $this->modelPrimaryKeyName . '</>');

        // Determine the last/largest value for love_reactant_id for this model
        $maxReactantId = $this->modelName::max('love_reactant_id');

        // Set up some counters
        $this->modelsAlreadyRegistered = 0;
        $this->modelsRegistered = 0;

        // If specific model IDs are passed into the command, use those
        if ($this->modelIds) {
            $models = $this->modelName::whereIn($this->modelPrimaryKeyName, explode(',', $this->modelIds))->get();
        } else {
            // Otherwise, get all of them
            $models = $this->modelName::all();
        }

        // Set up the progress bar
        $progressBar = $this->output->createProgressBar($models->count());;
        $progressBar->setFormat("            Records: %current%/%max% %bar% %percent:3s%%\n\n");
        $progressBar->setBarCharacter($done = "\033[32m●\033[0m");
        $progressBar->setEmptyBarCharacter($empty = "\033[31m●\033[0m");
        $progressBar->setBarCharacter($done = "\033[32m●\033[0m");

        // Process the models, registering the ones that need it
        foreach ($models as $model) {
            if ($model->isRegisteredAsLoveReactant()) {
                $this->modelsAlreadyRegistered++;
            } else {
                $model->registerAsLoveReactant();
                $this->modelsRegistered++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        // Show the results, and bail
        $this->renderTable();

        return 0;
    }

    public function renderTable(): void
    {
        $headers = ['Namespace', 'Models skipped', 'Models Registered'];

        $data = [[
            $this->modelName, $this->modelsAlreadyRegistered, $this->modelsRegistered,
        ]];

        $this->table($headers, $data);
    }
}
