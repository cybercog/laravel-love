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
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class Recount extends Command
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
    protected $description = 'Recount likes and dislikes of the likeable models';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    public function handle(Dispatcher $events): void
    {
        if ($reactionType = $this->argument('type')) {
            $reactionType = ReactionType::fromName($reactionType);
        }

        if ($modelType = $this->argument('model')) {
            $modelType = $this->normalizeModelType($modelType);
        }

        $reactants = Reactant::query()->get();
        foreach ($reactants as $reactant) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query = $reactant->reactions();

            if ($reactionType) {
                $query->where('reaction_type_id', $reactionType->getKey());
            }

            if ($modelType) {
                $query->whereHas('reactant', function (Builder $reactantQuery) use ($modelType) {
                    $reactantQuery->where('type', $modelType);
                });
            }

            foreach ($reactant->getReactionCounters() as $counter) {
                // TODO: Refactor it. Make type safe
                if ($counter->reaction_type_id != $reactionType->getKey()) {
                    continue;
                }
                // TODO: What to do if we asked to recount only reactions of exact reactant type?
                // TODO: Maybe we just need to set their values to 0?
                $counter->delete();
            }

            $reactions = $query->get();

            foreach ($reactions as $reaction) {
                (new ReactionCounterService($reactant))
                    ->incrementCounterOfType($reaction->getType());
            }
        }
    }

    /**
     * Normalize likeable model type.
     *
     * @param string $modelType
     * @return string
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    protected function normalizeModelType(string $modelType): string
    {
        $model = $this->newModelFromType($modelType);
        $modelType = $model->getMorphClass();

        if (!$model instanceof ReactableContract) {
            throw ReactableInvalid::notImplementInterface($modelType);
        }

        return $modelType;
    }

    /**
     * Instantiate model from type or morph map value.
     *
     * @param string $modelType
     * @return mixed
     *
     * @throws \Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid
     */
    private function newModelFromType(string $modelType)
    {
        if (class_exists($modelType)) {
            return new $modelType;
        }

        $morphMap = Relation::morphMap();

        if (!isset($morphMap[$modelType])) {
            throw ReactableInvalid::notExists($modelType);
        }

        $modelClass = $morphMap[$modelType];

        return new $modelClass;
    }
}
