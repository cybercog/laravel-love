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

namespace Cog\Contracts\Love\Likeable\Models;

/**
 * Interface Likeable.
 *
 * @package Cog\Contracts\Love\Likeable\Models
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
     * @return mixed
     */
    public function likesAndDislikes();

    /**
     * Collection of the likes on this record.
     *
     * @return mixed
     */
    public function likes();

    /**
     * Collection of the dislikes on this record.
     *
     * @return mixed
     */
    public function dislikes();

    /**
     * Counter is a record that stores the total likes for the morphed record.
     *
     * @return mixed
     */
    public function likesCounter();

    /**
     * Counter is a record that stores the total dislikes for the morphed record.
     *
     * @return mixed
     */
    public function dislikesCounter();

    /**
     * Fetch users who liked entity.
     *
     * @todo Do we need to rely on the Laravel Collections here?
     * @return \Illuminate\Support\Collection
     */
    public function collectLikers();

    /**
     * Fetch users who disliked entity.
     *
     * @todo Do we need to rely on the Laravel Collections here?
     * @return \Illuminate\Support\Collection
     */
    public function collectDislikers();

    /**
     * Add a like for model by the given user.
     *
     * @param null|string|int $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function likeBy($userId = null);

    /**
     * Add a dislike for model by the given user.
     *
     * @param null|string|int $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function dislikeBy($userId = null);

    /**
     * Remove a like from this record for the given user.
     *
     * @param null|string|int $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function unlikeBy($userId = null);

    /**
     * Remove a dislike from this record for the given user.
     *
     * @param null|string|int $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function undislikeBy($userId = null);

    /**
     * Toggle like for model by the given user.
     *
     * @param null|string|int $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function toggleLikeBy($userId = null);

    /**
     * Toggle dislike for model by the given user.
     *
     * @param null|string|int $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function toggleDislikeBy($userId = null);

    /**
     * Delete likes related to the current record.
     *
     * @return void
     */
    public function removeLikes();

    /**
     * Delete dislikes related to the current record.
     *
     * @return void
     */
    public function removeDislikes();

    /**
     * Has the user already liked likeable model.
     *
     * @param null|string|int $userId
     * @return bool
     */
    public function isLikedBy($userId = null): bool;

    /**
     * Has the user already disliked likeable model.
     *
     * @param null|string|int $userId
     * @return bool
     */
    public function isDislikedBy($userId = null): bool;
}
