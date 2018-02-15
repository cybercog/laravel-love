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
     * Add a like for the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function like(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->addLikeTo($likeable, LikeType::LIKE, $this);
    }

    /**
     * Add a dislike for the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function dislike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->addLikeTo($likeable, LikeType::DISLIKE, $this);
    }

    /**
     * Remove a like from the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function unlike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->removeLikeFrom($likeable, LikeType::LIKE, $this);
    }

    /**
     * Remove a dislike from the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function undislike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->removeLikeFrom($likeable, LikeType::DISLIKE, $this);
    }

    /**
     * Toggle like of the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function toggleLike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->toggleLikeOf($likeable, LikeType::LIKE, $this);
    }

    /**
     * Toggle dislike of the Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function toggleDislike(LikeableContract $likeable)
    {
        app(LikeableServiceContract::class)->toggleLikeOf($likeable, LikeType::DISLIKE, $this);
    }

    /**
     * Determine if Liker has liked Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function hasLiked(LikeableContract $likeable): bool
    {
        return app(LikeableServiceContract::class)->isLiked($likeable, LikeType::LIKE, $this);
    }

    /**
     * Determine if Liker has disliked Likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return bool
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function hasDisliked(LikeableContract $likeable): bool
    {
        return app(LikeableServiceContract::class)->isLiked($likeable, LikeType::DISLIKE, $this);
    }
}
