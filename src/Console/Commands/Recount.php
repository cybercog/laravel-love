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

use Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

final class Recount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'love:recount {model?} {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recount reactions of the reactable models';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    public function handle(
        Dispatcher $events
    ): void {
        if ($modelType = $this->argument('model')) {
            $modelType = $this->normalizeModelType($modelType);
        }

        if ($reactionType = $this->argument('type')) {
            $reactionType = ReactionType::fromName($reactionType);
        }

        $reactants = Reactant::query()->get();
        // TODO: What to do if we asked to recount only reactions of exact reactant type?
        foreach ($reactants as $reactant) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query = $reactant->reactions();

            if ($reactionType) {
                $query->where('reaction_type_id', $reactionType->getId());
            }

            if ($modelType) {
                $query->whereHas('reactant', function (Builder $reactantQuery) use ($modelType) {
                    $reactantQuery->where('type', $modelType);
                });
            }

            $counters = $reactant->getReactionCounters();

            /** @var \Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter $counter */
            foreach ($counters as $counter) {
                if (!$counter->isReactionOfType($reactionType)) {
                    continue;
                }

                $counter->update([
                    'count' => 0,
                    'weight' => 0,
                ]);
            }

            $service = new ReactionCounterService($reactant);
            $service->createMissingCounters($counters);

            $reactions = $query->get();

            foreach ($reactions as $reaction) {
                $service->addReaction($reaction);
            }

            $this->recountTotals($reactant);
        }
    }

    /**
     * Normalize reactable model type.
     *
     * @param string $modelType
     * @return string
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    private function normalizeModelType(
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

        $model = new $modelType;

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

    private function recountTotals(
        ReactantContract $reactant
    ): void {
        $counters = $reactant->getReactionCounters();
        $totalCount = 0;
        $totalWeight = 0;
        foreach ($counters as $counter) {
            $totalCount += $counter->getCount();
            $totalWeight += $counter->getWeight();
        }

        /** @var \Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal $total */
        $total = $reactant->getReactionTotal();
        $total->update([
            'count' => $totalCount,
            'weight' => $totalWeight,
        ]);
    }
}
