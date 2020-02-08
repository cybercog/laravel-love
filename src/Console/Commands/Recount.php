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
use Cog\Laravel\Love\Reactant\Jobs\RebuildAggregatesJob;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Symfony\Component\Console\Input\InputOption;

final class Recount extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'love:recount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recount reactions of the reactable models';

    /**
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    private $dispatcher;

    protected function getOptions(): array
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'The name of the reactable model'],
            ['type', null, InputOption::VALUE_OPTIONAL, 'The name of the reaction type'],
        ];
    }

    public function __construct(
        Dispatcher $dispatcher
    )
    {
        parent::__construct();
        $this->dispatcher = $dispatcher;
    }

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    public function handle(
    ): void {
        if ($reactableType = $this->option('model')) {
            $reactableType = $this->normalizeReactableModelType($reactableType);
        }

        if ($reactionType = $this->option('type')) {
            $reactionType = ReactionType::fromName($reactionType);
        }

        $reactants = $this->collectReactants($reactableType);

        $this->getOutput()->progressStart($reactants->count());
        foreach ($reactants as $reactant) {
            $this->dispatcher->dispatch(
                new RebuildAggregatesJob($reactant, $reactionType)
            );

            $this->getOutput()->progressAdvance();
        }
        $this->getOutput()->progressFinish();
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

    /**
     * @param string|null $reactableType
     * @return \Cog\Contracts\Love\Reactant\Models\Reactant[]|\Illuminate\Database\Eloquent\Collection
     */
    private function collectReactants(
        ?string $reactableType = null
    ): iterable {
        $reactantsQuery = Reactant::query();

        if ($reactableType !== null) {
            $reactantsQuery->where('type', $reactableType);
        }

        return $reactantsQuery->get();
    }
}
