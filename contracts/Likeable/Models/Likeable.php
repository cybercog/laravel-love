<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Contracts\Likeable\Likeable\Models;

/**
 * Interface Likeable.
 *
 * @package Cog\Contracts\Likeable\Likeable\Models
 */
interface Likeable
{
    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass();

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
     * Fetch users who liked entity.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collectLikers();

    /**
     * Fetch users who disliked entity.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collectDislikers();

    /**
     * Add a like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Laravel\Likeable\Exceptions\LikerNotDefinedException
     */
    public function like($userId = null);

    /**
     * Remove a like from this record for the given user.
     *
     * @param int|null $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Laravel\Likeable\Exceptions\LikerNotDefinedException
     */
    public function unlike($userId = null);

    /**
     * Toggle like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Laravel\Likeable\Exceptions\LikerNotDefinedException
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
     *
     * @throws \Cog\Laravel\Likeable\Exceptions\LikerNotDefinedException
     */
    public function dislike($userId = null);

    /**
     * Remove a dislike from this record for the given user.
     *
     * @param int|null $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Laravel\Likeable\Exceptions\LikerNotDefinedException
     */
    public function undislike($userId = null);

    /**
     * Toggle dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Laravel\Likeable\Exceptions\LikerNotDefinedException
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
