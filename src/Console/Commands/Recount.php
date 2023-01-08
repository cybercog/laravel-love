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
use Cog\Laravel\Love\Reactant\Jobs\RebuildReactionAggregatesJob;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherInterface;
use Illuminate\Contracts\Config\Repository as AppConfigRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'love:recount', description: 'Recount reactions of the reactable models')]
final class Recount extends Command
{
    private DispatcherInterface $dispatcher;

    /**
     * Get the console command options.
     *
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
                name: 'type',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The name of the reaction type',
            ),
            new InputOption(
                name: 'queue-connection',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The name of the queue connection',
            ),
        ];
    }

    /**
     * Execute the console command.
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    public function handle(
        DispatcherInterface $dispatcher,
        AppConfigRepositoryInterface $appConfigRepository,
    ): int {
        $this->dispatcher = $dispatcher;

        if ($reactableType = $this->option('model')) {
            $reactableType = $this->normalizeReactableModelType($reactableType);
        }

        if ($reactionType = $this->option('type')) {
            $reactionType = ReactionType::fromName($reactionType);
        }

        $queueConnectionName = $this->option('queue-connection');
        if ($queueConnectionName === null || $queueConnectionName === '') {
            $queueConnectionName = $appConfigRepository->get('queue.default');
        }

        $this->warn(
            "Rebuilding reaction aggregates using `$queueConnectionName` queue connection."
        );

        $reactants = $this->collectReactants($reactableType);

        $this->getOutput()->progressStart($reactants->count());
        foreach ($reactants as $reactant) {
            $this->dispatcher->dispatch(
                (new RebuildReactionAggregatesJob($reactant, $reactionType))
                    ->onConnection($queueConnectionName)
            );

            $this->getOutput()->progressAdvance();
        }
        $this->getOutput()->progressFinish();

        return self::SUCCESS;
    }

    /**
     * Normalize reactable model type.
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    private function normalizeReactableModelType(
        string $modelType,
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
        string $modelType,
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
        string $modelType,
    ): string {
        $morphMap = Relation::morphMap();

        if (!isset($morphMap[$modelType])) {
            throw ReactableInvalid::classNotExists($modelType);
        }

        return $morphMap[$modelType];
    }

    /**
     * Collect all reactants we want to affect.
     *
     * @param string|null $reactableType
     * @return \Cog\Contracts\Love\Reactant\Models\Reactant[]|\Illuminate\Database\Eloquent\Collection
     */
    private function collectReactants(
        ?string $reactableType = null,
    ): iterable {
        $reactantsQuery = Reactant::query();

        if ($reactableType !== null) {
            $reactantsQuery->where('type', $reactableType);
        }

        return $reactantsQuery->get();
    }
}
