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

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable;
use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
use Cog\Contracts\Love\LikeCounter\Models\LikeCounter as LikeCounterContract;
use Cog\Laravel\Love\Likeable\Services\LikeableService as LikeableServiceContract;
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
    protected $description = 'Recount likes and dislikes for the models';

    /**
     * Type of likes to be recounted.
     *
     * @var null|string
     */
    protected $likeType;

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
     * @throws \Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function handle(Dispatcher $events)
    {
        $model = $this->argument('model');
        $this->likeType = $this->argument('type');
        $this->service = app(LikeableServiceContract::class);

        if (empty($model)) {
            $this->recountLikesOfAllModelTypes();
        } else {
            $this->recountLikesOfModelType($model);
        }
    }

    /**
     * Recount likes of all model types.
     *
     * @return void
     *
     * @throws \Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    protected function recountLikesOfAllModelTypes()
    {
        $likeableTypes = app(LikeContract::class)->groupBy('likeable_type')->get();
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
     * @throws \Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    protected function recountLikesOfModelType($modelType)
    {
        $modelType = $this->normalizeModelType($modelType);

        $counters = $this->service->fetchLikesCounters($modelType, $this->likeType);

        $this->service->removeLikeCountersOfType($modelType, $this->likeType);

        DB::table(app(LikeCounterContract::class)->getTable())->insert($counters);

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
    protected function normalizeModelType($modelType): string
    {
        $morphMap = Relation::morphMap();

        if (class_exists($modelType)) {
            $model = new $modelType;
            $modelType = $model->getMorphClass();
        } else {
            if (!isset($morphMap[$modelType])) {
                throw InvalidLikeable::notExists($modelType);
            }

            $modelClass = $morphMap[$modelType];
            $model = new $modelClass;
        }

        if (!$model instanceof LikeableContract) {
            throw InvalidLikeable::notImplementInterface($modelType);
        }

        return $modelType;
    }
}
