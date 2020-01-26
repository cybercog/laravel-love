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
use Symfony\Component\Console\Input\InputOption;

final class RegisterExistingReacters extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'love:register-reacters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers any existing unregistered reacters (Models)';

    private $modelsRegistered = 0;

    private $modelsAlreadyRegistered = 0;

    protected function getOptions(): array
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'The name of the Reacterable model'],
            ['ids', null, InputOption::VALUE_IS_ARRAY, 'Comma-separated list of model IDs, or omit this argument for all IDs (e.g. `1,2,16,34`)'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $modelName = $this->option('model');
        $modelIds = $this->option('ids');

        $this->line("\n" . '<fg=yellow;options=underscore>Registering Reacters ...</>' . "\n");
        $this->line('       Target model: <fg=Cyan>' . $modelName . '</>');

        // Verify that the Model class actually exists
        if (!class_exists($modelName)) {
            $this->line('Model class exists?: <fg=red;options=bold>No</>');
            $errorMessage = 'Model not found! Check your spelling, and be sure to escape any namespace backslashes.';
            $this->line("\n" . '              <fg=red;options=bold>Error:</> <fg=red>' . $errorMessage . '</>' . "\n");

            return 1;
        }

        $this->line('Model class exists?: <fg=green>Yes</>');

        // Determine the primary key of the target model
        $modelPrimaryKeyName = (new $modelName)->getKeyName();
        $this->line('   Primary Key Name: <fg=Cyan>' . $modelPrimaryKeyName . '</>');

        // If specific model IDs are passed into the command, use those
        if ($modelIds) {
            $models = $modelName::whereIn($modelPrimaryKeyName, explode(',', $modelIds))->get();
        } else {
            // Otherwise, get all of them
            $models = $modelName::all();
        }

        // Set up the progress bar
        $progressBar = $this->output->createProgressBar($models->count());
        $progressBar->setFormat("            Records: %current%/%max% %bar% %percent:3s%%\n\n");
        $progressBar->setBarCharacter($done = "\033[32m●\033[0m");
        $progressBar->setEmptyBarCharacter($empty = "\033[31m●\033[0m");
        $progressBar->setBarCharacter($done = "\033[32m●\033[0m");

        // Process the models, registering the ones that need it
        foreach ($models as $model) {
            if ($model->isRegisteredAsLoveReacter()) {
                $this->modelsAlreadyRegistered++;
            } else {
                $model->registerAsLoveReacter();
                $this->modelsRegistered++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->renderTable($modelName);

        return 0;
    }

    private function renderTable(string $modelName): void
    {
        $headers = ['Namespace', 'Models skipped', 'Models Registered'];

        $data = [[
            $modelName, $this->modelsAlreadyRegistered, $this->modelsRegistered,
        ]];

        $this->table($headers, $data);
    }
}
