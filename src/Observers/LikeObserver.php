<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Observers;

use Cog\Likeable\Enums\LikeType;
use Cog\Likeable\Events\ModelWasDisliked;
use Cog\Likeable\Events\ModelWasLiked;
use Cog\Likeable\Events\ModelWasUndisliked;
use Cog\Likeable\Events\ModelWasUnliked;
use Cog\Likeable\Contracts\Like as LikeContract;
use Cog\Likeable\Contracts\LikeableService as LikeableServiceContract;

/**
 * Class LikeObserver.
 *
 * @package Cog\Likeable\Observers
 */
class LikeObserver
{
    /**
     * Handle the created event for the model.
     *
     * @param \Cog\Likeable\Contracts\Like $like
     * @return void
     */
    public function created(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new ModelWasLiked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->incrementLikeCount($like->likeable, $like->type_id);
        } else {
            event(new ModelWasDisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->incrementDislikeCount($like->likeable, $like->type_id);
        }
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param \Cog\Likeable\Contracts\Like $like
     * @return void
     */
    public function deleted(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new ModelWasUnliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementLikeCount($like->likeable, $like->type_id);
        } else {
            event(new ModelWasUndisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementDislikeCount($like->likeable, $like->type_id);
        }
    }
}
