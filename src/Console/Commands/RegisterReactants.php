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
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'love:register-reactants', description: 'Register Reactable models as Reactants')]
final class RegisterReactants extends Command
{
    /**
     * @return array<int, InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'model',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The name of the Reactable model',
            ),
            new InputOption(
                name: 'ids',
                mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                description: '(optional) Comma-separated list of model IDs (e.g. `--ids=1,2,16,34`)',
            ),
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $reactableType = $this->option('model');
        if ($reactableType === null) {
            $this->error('Option `--model` is required!');

            return self::FAILURE;
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

            return self::FAILURE;
        }

        return self::SUCCESS;
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
    ): ReactableInterface {
        if (!class_exists($modelType)) {
            $modelType = $this->findModelTypeInMorphMap($modelType);
        }

        $model = new $modelType();

        if (!$model instanceof ReactableInterface) {
            throw ReactableInvalid::notImplementInterface($modelType);
        }

        return $model;
    }

    /**
     * Find model type in morph mappings registry.
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
        if (!isset($modelIds[0])) {
            return $modelIds;
        }

        $firstItem = $modelIds[0];

        if (is_string($firstItem) && strpos($firstItem, ',')) {
            $modelIds = explode(',', $firstItem);
        }

        return $modelIds;
    }

    /**
     * @param \Cog\Contracts\Love\Reactable\Models\Reactable|\Illuminate\Database\Eloquent\Model $reactableModel
     * @param array $modelIds
     * @return Collection
     */
    private function collectModels(
        ReactableInterface $reactableModel,
        array $modelIds
    ): Collection {
        $query = $reactableModel
            ->query()
            ->whereNull('love_reactant_id');

        if (!empty($modelIds)) {
            $query->whereKey($modelIds);
        }

        return $query->get();
    }

    private function registerModelsAsReactants(Collection $models): void
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
