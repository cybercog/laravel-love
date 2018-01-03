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

namespace Cog\Laravel\Love\Like\Observers;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Contracts\Love\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Laravel\Love\Like\Enums\LikeType;
use Cog\Laravel\Love\Likeable\Events\LikeableWasDisliked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasLiked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasUndisliked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasUnliked;

/**
 * Class LikeObserver.
 *
 * @package Cog\Laravel\Love\Like\Observers
 */
class LikeObserver
{
    /**
     * Handle the created event for the model.
     *
     * @param \Cog\Contracts\Love\Like\Models\Like $like
     * @return void
     */
    public function created(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new LikeableWasLiked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->incrementLikesCount($like->likeable);
        } else {
            event(new LikeableWasDisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->incrementDislikesCount($like->likeable);
        }
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param \Cog\Contracts\Love\Like\Models\Like $like
     * @return void
     */
    public function deleted(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new LikeableWasUnliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementLikesCount($like->likeable);
        } else {
            event(new LikeableWasUndisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementDislikesCount($like->likeable);
        }
    }
}
