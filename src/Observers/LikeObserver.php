<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Laravel\Likeable\Observers;

use Cog\Laravel\Likeable\Enums\LikeType;
use Cog\Laravel\Likeable\Events\ModelWasDisliked;
use Cog\Laravel\Likeable\Events\ModelWasLiked;
use Cog\Laravel\Likeable\Events\ModelWasUndisliked;
use Cog\Laravel\Likeable\Events\ModelWasUnliked;
use Cog\Contracts\Likeable\Like as LikeContract;
use Cog\Contracts\Likeable\LikeableService as LikeableServiceContract;

/**
 * Class LikeObserver.
 *
 * @package Cog\Laravel\Likeable\Observers
 */
class LikeObserver
{
    /**
     * Handle the created event for the model.
     *
     * @param \Cog\Contracts\Likeable\Like $like
     * @return void
     */
    public function created(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new ModelWasLiked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->incrementLikesCount($like->likeable);
        } else {
            event(new ModelWasDisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->incrementDislikesCount($like->likeable);
        }
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param \Cog\Contracts\Likeable\Like $like
     * @return void
     */
    public function deleted(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new ModelWasUnliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementLikesCount($like->likeable);
        } else {
            event(new ModelWasUndisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementDislikesCount($like->likeable);
        }
    }
}
