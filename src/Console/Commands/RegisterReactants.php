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

    protected function getOptions(): array
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'The name of the Reactable model'],
            ['ids', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, '(optional) Comma-separated list of model IDs (e.g. `--ids=1,2,16,34`)'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $reactableType = $this->option('model');
        if ($reactableType === null) {
            $this->error('Option `--model` is required!');

            return 1;
        }

        try {
            $reactableModel = $this->reactableModelFromType($reactableType);

            $modelIds = $this->option('ids');
            $modelIds = $this->normalizeIds($modelIds);

            $models = $this->collectModels($reactableModel, $modelIds);

            $this->info(sprintf('Models registering as Reactants %s', PHP_EOL));
            $this->line(sprintf('Model Type: <fg=Cyan>%s</>', get_class($reactableModel)));

            $this->registerModelsAsReactants($models);

            $this->info('Models has been registered as Reactants');
        } catch (ReactableInvalid $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        return 0;
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

    private function normalizeIds(array $modelIds): array
    {
        if (isset($modelIds[0]) && strpos($modelIds[0], ',')) {
            $modelIds = explode(',', $modelIds[0]);
        }

        return $modelIds;
    }

    /**
     * @param \Cog\Contracts\Love\Reactable\Models\Reactable|\Illuminate\Database\Eloquent\Model $reactableModel
     * @param array $modelIds
     * @return iterable
     */
    private function collectModels(
        ReactableContract $reactableModel,
        array $modelIds
    ): iterable {
        $query = $reactableModel
            ->query()
            ->whereNull('love_reactant_id');

        if (!empty($modelIds)) {
            $query->whereKey($modelIds);
        }

        return $query->get();
    }

    private function registerModelsAsReactants(iterable $models): void
    {
        $collectedCount = $models->count();
        $progressBar = $this->output->createProgressBar($collectedCount);

        foreach ($models as $model) {
            $model->registerAsLoveReactant();
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line(PHP_EOL);
    }
}
