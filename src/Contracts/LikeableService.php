<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Contracts;

use Cog\Likeable\Contracts\Likeable as LikeableContract;

/**
 * Interface LikeableService.
 *
 * @package Cog\Likeable\Contracts
 */
interface LikeableService
{
    /**
     * Add a like to likeable model by user.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @param string $type
     * @param string $userId
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function addLikeTo(LikeableContract $likeable, $type, $userId);

    /**
     * Remove a like to likeable model by user.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @param string $type
     * @param int|null $userId
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function removeLikeFrom(LikeableContract $likeable, $type, $userId);

    /**
     * Toggle like for model by the given user.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @param string $type
     * @param string $userId
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function toggleLikeOf(LikeableContract $likeable, $type, $userId);

    /**
     * Has the user already liked likeable model.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @param string $type
     * @param int|null $userId
     * @return bool
     */
    public function isLiked(LikeableContract $likeable, $type, $userId);

    /**
     * Decrement the total like count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function decrementLikesCount(LikeableContract $likeable);

    /**
     * Increment the total like count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function incrementLikesCount(LikeableContract $likeable);

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function decrementDislikesCount(LikeableContract $likeable);

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function incrementDislikesCount(LikeableContract $likeable);

    /**
     * Remove like counters by likeable type.
     *
     * @param string $likeableType
     * @param string|null $type
     * @return void
     */
    public function removeLikeCountersOfType($likeableType, $type = null);

    /**
     * Remove all likes from likeable model.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @param string $type
     * @return void
     */
    public function removeModelLikes(LikeableContract $likeable, $type);

    /**
     * Get collection of users who liked entity.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @return \Illuminate\Support\Collection
     */
    public function collectLikersOf(LikeableContract $likeable);

    /**
     * Get collection of users who disliked entity.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @return \Illuminate\Support\Collection
     */
    public function collectDislikersOf(LikeableContract $likeable);

    /**
     * Fetch likes counters data.
     *
     * @param string $likeableType
     * @param string $likeType
     * @return array
     */
    public function fetchLikesCounters($likeableType, $likeType);
}
