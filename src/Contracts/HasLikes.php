<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Contracts;

/**
 * Interface HasLikes.
 *
 * @package Cog\Likeable\Contracts
 */
interface HasLikes
{
    /**
     * Collection of the likes on this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likesAndDislikes();

    /**
     * Collection of the likes on this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes();

    /**
     * Collection of the dislikes on this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function dislikes();

    /**
     * Counter is a record that stores the total likes for the morphed record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function likesCounter();

    /**
     * Counter is a record that stores the total dislikes for the morphed record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function dislikesCounter();

    /**
     * Model likesCount attribute.
     *
     * @return int
     */
    public function getLikesCountAttribute();

    /**
     * Model dislikesCount attribute.
     *
     * @return int
     */
    public function getDislikesCountAttribute();

    /**
     * Did the currently logged in user like this model.
     *
     * @return bool
     */
    public function getLikedAttribute();

    /**
     * Did the currently logged in user dislike this model.
     *
     * @return bool
     */
    public function getDislikedAttribute();

    /**
     * Difference between likes and dislikes count.
     *
     * @return int
     */
    public function getLikesDiffDislikesCountAttribute();

    /**
     * Fetch records that are liked by a given user id.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int|null $userId
     * @return \Illuminate\Database\Query\Builder
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function scopeWhereLikedBy($query, $userId = null);

    /**
     * Fetch records that are disliked by a given user id.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int|null $userId
     * @return \Illuminate\Database\Query\Builder
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function scopeWhereDislikedBy($query, $userId = null);

    /**
     * Add a like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function like($userId = null);

    /**
     * Remove a like from this record for the given user.
     *
     * @param int|null $userId If null will use currently logged in user.
     * @return void
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function unlike($userId = null);

    /**
     * Toggle like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function likeToggle($userId = null);

    /**
     * Has the user already liked likeable model.
     *
     * @param int|null $userId
     * @return bool
     */
    public function liked($userId = null);

    /**
     * Delete likes related to the current record.
     *
     * @return void
     */
    public function removeLikes();

    /**
     * Add a dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function dislike($userId = null);

    /**
     * Remove a dislike from this record for the given user.
     *
     * @param int|null $userId If null will use currently logged in user.
     * @return void
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function undislike($userId = null);

    /**
     * Toggle dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function dislikeToggle($userId = null);

    /**
     * Has the user already disliked likeable model.
     *
     * @param int|null $userId
     * @return bool
     */
    public function disliked($userId = null);

    /**
     * Delete dislikes related to the current record.
     *
     * @return void
     */
    public function removeDislikes();
}
