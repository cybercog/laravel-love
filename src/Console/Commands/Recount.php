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

use Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable;
use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
use Cog\Contracts\Love\LikeCounter\Models\LikeCounter as LikeCounterContract;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

/**
 * Class Recount.
 *
 * @package Cog\Laravel\Love\Console\Commands
 */
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
     * Type of reactions to be recounted.
     *
     * @var null|string
     */
    protected $reactionType;

    /**
     * Likeable service.
     *
     * @var \Cog\Contracts\Love\Likeable\Services\LikeableService
     */
    protected $service;

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable
     */
    public function handle(Dispatcher $events)
    {
        $model = $this->argument('model');
        if ($model) {
            $model = $this->normalizeModelType($model);
        }
        if ($type = $this->argument('type')) {
            $this->reactionType = ReactionType::fromName($type);
        }
//        $this->service = app(LikeableServiceContract::class);

        $reactants = Reactant::all();
        foreach ($reactants as $reactant) {
            $query = $reactant->reactions();

            if ($this->reactionType) {
                $query->where('reaction_type_id', $this->reactionType->getKey());
            }

            if ($model) {
                $query->whereHas('reactant', function ($q) use ($model) {
                    $q->where('type', $model);
                });
            }

            $reactions = $query->get();

            foreach ($reactions as $reaction) {
                (new ReactionCounterService($reactant))
                    ->incrementCounterOfType($reaction->getType());
            }

        }

//        if (empty($model)) {
//            $this->recountLikesOfAllModelTypes();
//        } else {
//            $this->recountLikesOfModelType($model);
//        }
    }

    /**
     * Recount likes of all model types.
     *
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable
     */
    protected function recountLikesOfAllModelTypes()
    {
        $likeableTypes = app(Reaction::class)->groupBy('likeable_type')->get();
        foreach ($likeableTypes as $like) {
            $this->recountLikesOfModelType($like->likeable_type);
        }
    }

    /**
     * Recount likes of model type.
     *
     * @param string $modelType
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable
     */
    protected function recountLikesOfModelType(string $modelType)
    {
        $modelType = $this->normalizeModelType($modelType);

        $counters = $this->service->fetchLikesCounters($modelType, $this->reactionType);

        $this->service->removeLikeCountersOfType($modelType, $this->reactionType);

        $likesCounterTable = app(LikeCounterContract::class)->getTable();

        DB::table($likesCounterTable)->insert($counters);

        $this->info('All [' . $modelType . '] records likes has been recounted.');
    }

    /**
     * Normalize likeable model type.
     *
     * @param string $modelType
     * @return string
     *
     * @throws \Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable
     */
    protected function normalizeModelType(string $modelType): string
    {
        $model = $this->newModelFromType($modelType);
        $modelType = $model->getMorphClass();

        // TODO: Check for instance of ReactantContract
        if (!$model instanceof LikeableContract) {
            throw InvalidLikeable::notImplementInterface($modelType);
        }

        return $modelType;
    }

    /**
     * Instantiate model from type or morph map value.
     *
     * @param string $modelType
     * @return mixed
     *
     * @throws \Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable
     */
    private function newModelFromType(string $modelType)
    {
        if (class_exists($modelType)) {
            return new $modelType;
        }

        $morphMap = Relation::morphMap();

        if (!isset($morphMap[$modelType])) {
            throw InvalidLikeable::notExists($modelType);
        }

        $modelClass = $morphMap[$modelType];

        return new $modelClass;
    }
}
