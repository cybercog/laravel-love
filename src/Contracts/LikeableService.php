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

use Cog\Likeable\Contracts\HasLikes as HasLikesContract;
use Illuminate\Database\Eloquent\Builder;

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
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @param string $type
     * @param string $userId
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function addLikeTo(HasLikesContract $model, $type, $userId);

    /**
     * Remove a like to likeable model by user.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @param string $type
     * @param int|null $userId
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function removeLikeFrom(HasLikesContract $model, $type, $userId);

    /**
     * Toggle like for model by the given user.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @param string $type
     * @param string $userId
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function toggleLikeOf(HasLikesContract $model, $type, $userId);

    /**
     * Has the user already liked likeable model.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @param string $type
     * @param int|null $userId
     * @return bool
     */
    public function isLiked(HasLikesContract $model, $type, $userId);

    /**
     * Decrement the total like count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function decrementLikesCount(HasLikesContract $model);

    /**
     * Increment the total like count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function incrementLikesCount(HasLikesContract $model);

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function decrementDislikesCount(HasLikesContract $model);

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function incrementDislikesCount(HasLikesContract $model);

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
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @param string $type
     * @return void
     */
    public function removeModelLikes(HasLikesContract $model, $type);

    /**
     * Fetch records that are liked by a given user id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function scopeWhereLikedBy(Builder $query, $type, $userId);

    /**
     * Fetch records sorted by likes count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByLikesCount(Builder $query, $direction = 'desc');

    /**
     * Fetch likes counters data.
     *
     * @param string $likeableType
     * @param string $likeType
     * @return array
     */
    public function fetchLikesCounters($likeableType, $likeType);
}
