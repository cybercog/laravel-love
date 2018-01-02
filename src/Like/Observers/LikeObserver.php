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
use Cog\Laravel\Love\Likeable\Events\ModelWasDisliked;
use Cog\Laravel\Love\Likeable\Events\ModelWasLiked;
use Cog\Laravel\Love\Likeable\Events\ModelWasUndisliked;
use Cog\Laravel\Love\Likeable\Events\ModelWasUnliked;

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
     * @param \Cog\Contracts\Love\Like\Models\Like $like
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
