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

namespace Cog\Laravel\Love\Liker\Models\Traits;

use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
use Cog\Contracts\Love\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Laravel\Love\Like\Enums\LikeType;

/**
 * Trait Liker.
 *
 * @package Cog\Laravel\Love\Liker\Models\Traits
 */
trait Liker
{
    /**
     * Add a like for model by the given user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function like(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->addLikeTo($likeable, LikeType::LIKE, $this);
    }

    /**
     * Remove a like from this record for the given user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     */
    public function unlike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->removeLikeFrom($likeable, LikeType::LIKE, $this);
    }

    /**
     * Toggle like for model by the given user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     */
    public function toggleLike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->toggleLikeOf($likeable, LikeType::LIKE, $this);
    }

    /**
     * Has the user already liked likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     */
    public function hasLiked(LikeableContract $likeable): bool
    {
        return app(LikeableServiceContract::class)->isLiked($likeable, LikeType::LIKE, $this);
    }

    /**
     * Add a dislike for model by the given user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     */
    public function dislike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->addLikeTo($likeable, LikeType::DISLIKE, $this);
    }

    /**
     * Remove a dislike from this record for the given user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     */
    public function undislike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->removeLikeFrom($likeable, LikeType::DISLIKE, $this);
    }

    /**
     * Toggle dislike for model by the given user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     */
    public function toggleDislike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->toggleLikeOf($likeable, LikeType::DISLIKE, $this);
    }

    /**
     * Has the user already disliked likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     */
    public function hasDisliked(LikeableContract $likeable): bool
    {
        return app(LikeableServiceContract::class)->isLiked($likeable, LikeType::DISLIKE, $this);
    }
}
