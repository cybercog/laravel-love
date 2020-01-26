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

use Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Symfony\Component\Console\Input\InputOption;

final class RegisterReactants extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'love:register-reactants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register Reactable models as Reactants';

    private $modelsRegistered = 0;

    private $modelsAlreadyRegistered = 0;

    protected function getOptions(): array
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'The name of the Reactable model'],
            ['ids', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Comma-separated list of model IDs, or omit this argument for all IDs (e.g. `1,2,16,34`)'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        try {
            $reactableType = $this->option('model');
            if ($reactableType === null) {
                $this->error('Option `--model` is required!');

                return 1;
            }
            $reactableType = $this->normalizeReactableModelType($reactableType);

            $modelIds = $this->option('ids');

            $this->line("\n" . '<fg=yellow;options=underscore>Registering Reactants ...</>' . "\n");
            $this->line('       Target model: <fg=Cyan>' . $reactableType . '</>');
            $this->line('Model class exists?: <fg=green>Yes</>');

            // Determine the primary key of the target model
            $modelPrimaryKeyName = (new $reactableType())->getKeyName();
            $this->line('   Primary Key Name: <fg=Cyan>' . $modelPrimaryKeyName . '</>');

            // If specific model IDs are passed into the command, use those
            if ($modelIds) {
                $models = $reactableType::whereIn($modelPrimaryKeyName, explode(',', $modelIds))->get();
            } else {
                // Otherwise, get all of them
                $models = $reactableType::all();
            }

            // Set up the progress bar
            $progressBar = $this->output->createProgressBar($models->count());
            $progressBar->setFormat("            Records: %current%/%max% %bar% %percent:3s%%\n\n");
            $progressBar->setBarCharacter($done = "\033[32m●\033[0m");
            $progressBar->setEmptyBarCharacter($empty = "\033[31m●\033[0m");
            $progressBar->setBarCharacter($done = "\033[32m●\033[0m");

            // Process the models, registering the ones that need it
            foreach ($models as $model) {
                if ($model->isRegisteredAsLoveReactant()) {
                    $this->modelsAlreadyRegistered++;
                } else {
                    //                $model->registerAsLoveReactant();
                    $this->modelsRegistered++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();

            $this->renderTable($reactableType);
        } catch (ReactableInvalid $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        return 0;
    }

    private function renderTable(string $reactableType): void
    {
        $headers = ['Namespace', 'Models skipped', 'Models Registered'];

        $data = [[
            $reactableType, $this->modelsAlreadyRegistered, $this->modelsRegistered,
        ]];

        $this->table($headers, $data);
    }

    /**
     * Normalize reactable model type.
     *
     * @param string $modelType
     * @return string
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    private function normalizeReactableModelType(
        string $modelType
    ): string {
        return $this
            ->reactableModelFromType($modelType)
            ->getMorphClass();
    }

    /**
     * Instantiate model from type or morph map value.
     *
     * @param string $modelType
     * @return \Cog\Contracts\Love\Reactable\Models\Reactable|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    private function reactableModelFromType(
        string $modelType
    ): ReactableContract {
        if (!class_exists($modelType)) {
            $modelType = $this->findModelTypeInMorphMap($modelType);
        }

        $model = new $modelType();

        if (!$model instanceof ReactableContract) {
            throw ReactableInvalid::notImplementInterface($modelType);
        }

        return $model;
    }

    /**
     * Find model type in morph mappings registry.
     *
     * @param string $modelType
     * @return string
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    private function findModelTypeInMorphMap(
        string $modelType
    ): string {
        $morphMap = Relation::morphMap();

        if (!isset($morphMap[$modelType])) {
            throw ReactableInvalid::classNotExists($modelType);
        }

        return $morphMap[$modelType];
    }
}
