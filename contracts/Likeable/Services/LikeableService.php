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

namespace Cog\Contracts\Love\Likeable\Services;

use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;

/**
 * Interface LikeableService.
 *
 * @package Cog\Contracts\Love\Likeable\Services
 */
interface LikeableService
{
    /**
     * Add a like to likeable model by user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $type
     * @param string $userId
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function addLikeTo(LikeableContract $likeable, $type, $userId);

    /**
     * Remove a like to likeable model by user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $type
     * @param null|string|int $userId
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function removeLikeFrom(LikeableContract $likeable, $type, $userId);

    /**
     * Toggle like for model by the given user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $type
     * @param string $userId
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function toggleLikeOf(LikeableContract $likeable, $type, $userId);

    /**
     * Has the user already liked likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $type
     * @param null|int $userId
     * @return bool
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function isLiked(LikeableContract $likeable, $type, $userId): bool;

    /**
     * Decrement the total like count stored in the counter.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function decrementLikesCount(LikeableContract $likeable);

    /**
     * Increment the total like count stored in the counter.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function incrementLikesCount(LikeableContract $likeable);

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function decrementDislikesCount(LikeableContract $likeable);

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function incrementDislikesCount(LikeableContract $likeable);

    /**
     * Remove like counters by likeable type.
     *
     * @param string $likeableType
     * @param null|string $type
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function removeLikeCountersOfType($likeableType, $type = null);

    /**
     * Remove all likes from likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $type
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function removeModelLikes(LikeableContract $likeable, $type);

    /**
     * Get collection of users who liked entity.
     *
     * @todo Do we need to rely on the Laravel Collections here?
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return \Illuminate\Support\Collection
     */
    public function collectLikersOf(LikeableContract $likeable);

    /**
     * Get collection of users who disliked entity.
     *
     * @todo Do we need to rely on the Laravel Collections here?
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return \Illuminate\Support\Collection
     */
    public function collectDislikersOf(LikeableContract $likeable);

    /**
     * Fetch likes counters data.
     *
     * @param string $likeableType
     * @param string $likeType
     * @return array
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function fetchLikesCounters($likeableType, $likeType): array;
}
