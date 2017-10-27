<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Traits;

use Cog\Likeable\Contracts\Like as LikeContract;
use Cog\Likeable\Contracts\LikeableService as LikeableServiceContract;
use Cog\Likeable\Contracts\LikeCounter as LikeCounterContract;
use Cog\Likeable\Enums\LikeType;
use Cog\Likeable\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Likeable.
 *
 * @package Cog\Likeable\Traits
 */
trait Likeable
{
    /**
     * Boot the Likeable trait for a model.
     *
     * @return void
     */
    public static function bootLikeable()
    {
        static::observe(ModelObserver::class);
    }

    /**
     * Collection of likes and dislikes on this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likesAndDislikes()
    {
        return $this->morphMany(app(LikeContract::class), 'likeable');
    }

    /**
     * Collection of likes on this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes()
    {
        return $this->likesAndDislikes()->where('type_id', LikeType::LIKE);
    }

    /**
     * Collection of dislikes on this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function dislikes()
    {
        return $this->likesAndDislikes()->where('type_id', LikeType::DISLIKE);
    }

    /**
     * Counter is a record that stores the total likes for the morphed record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function likesCounter()
    {
        return $this->morphOne(app(LikeCounterContract::class), 'likeable')
            ->where('type_id', LikeType::LIKE);
    }

    /**
     * Counter is a record that stores the total dislikes for the morphed record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function dislikesCounter()
    {
        return $this->morphOne(app(LikeCounterContract::class), 'likeable')
            ->where('type_id', LikeType::DISLIKE);
    }

    /**
     * Collection of likes on this record by the logged in user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function userLikes()
    {
        return $this->likesAndDislikes()->where([
            'type_id' => LikeType::LIKE,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Collection of dislikes on this record by the logged in user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function userDislikes()
    {
        return $this->likesAndDislikes()->where([
            'type_id' => LikeType::DISLIKE,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Collection of likes and dislikes on this record by the logged in user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function userLikesAndDislikes()
    {
        return $this->likesAndDislikes()->where([
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Fetch users who liked entity.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collectLikers()
    {
        return app(LikeableServiceContract::class)->collectLikersOf($this);
    }

    /**
     * Fetch users who disliked entity.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collectDislikers()
    {
        return app(LikeableServiceContract::class)->collectDislikersOf($this);
    }

    /**
     * Model likesCount attribute.
     *
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likesCounter ? $this->likesCounter->count : 0;
    }

    /**
     * Model dislikesCount attribute.
     *
     * @return int
     */
    public function getDislikesCountAttribute()
    {
        return $this->dislikesCounter ? $this->dislikesCounter->count : 0;
    }

    /**
     * Did the currently logged in user like this model.
     *
     * @return bool
     */
    public function getLikedAttribute()
    {
        return $this->liked();
    }

    /**
     * Did the currently logged in user dislike this model.
     *
     * @return bool
     */
    public function getDislikedAttribute()
    {
        return $this->disliked();
    }

    /**
     * Difference between likes and dislikes count.
     *
     * @return int
     */
    public function getLikesDiffDislikesCountAttribute()
    {
        return $this->likesCount - $this->dislikesCount;
    }

    /**
     * Fetch records that are liked by a given user id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function scopeWhereLikedBy(Builder $query, $userId = null)
    {
        return app(LikeableServiceContract::class)
            ->scopeWhereLikedBy($query, LikeType::LIKE, $userId);
    }

    /**
     * Fetch records that are disliked by a given user id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function scopeWhereDislikedBy(Builder $query, $userId = null)
    {
        return app(LikeableServiceContract::class)
            ->scopeWhereLikedBy($query, LikeType::DISLIKE, $userId);
    }

    /**
     * Fetch records sorted by likes count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByLikesCount(Builder $query, $direction = 'desc')
    {
        return app(LikeableServiceContract::class)
            ->scopeOrderByLikesCount($query, LikeType::LIKE, $direction);
    }

    /**
     * Fetch records sorted by likes count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByDislikesCount(Builder $query, $direction = 'desc')
    {
        return app(LikeableServiceContract::class)
            ->scopeOrderByLikesCount($query, LikeType::DISLIKE, $direction);
    }

    /**
     * Add a like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function like($userId = null)
    {
        app(LikeableServiceContract::class)->addLikeTo($this, LikeType::LIKE, $userId);
    }

    /**
     * Remove a like from this record for the given user.
     *
     * @param int|null $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function unlike($userId = null)
    {
        app(LikeableServiceContract::class)->removeLikeFrom($this, LikeType::LIKE, $userId);
    }

    /**
     * Toggle like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function likeToggle($userId = null)
    {
        app(LikeableServiceContract::class)->toggleLikeOf($this, LikeType::LIKE, $userId);
    }

    /**
     * Has the user already liked likeable model.
     *
     * @param int|null $userId
     * @return bool
     */
    public function liked($userId = null)
    {
        return app(LikeableServiceContract::class)->isLiked($this, LikeType::LIKE, $userId);
    }

    /**
     * Delete likes related to the current record.
     *
     * @return void
     */
    public function removeLikes()
    {
        app(LikeableServiceContract::class)->removeModelLikes($this, LikeType::LIKE);
    }

    /**
     * Add a dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function dislike($userId = null)
    {
        app(LikeableServiceContract::class)->addLikeTo($this, LikeType::DISLIKE, $userId);
    }

    /**
     * Remove a dislike from this record for the given user.
     *
     * @param int|null $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function undislike($userId = null)
    {
        app(LikeableServiceContract::class)->removeLikeFrom($this, LikeType::DISLIKE, $userId);
    }

    /**
     * Toggle dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function dislikeToggle($userId = null)
    {
        app(LikeableServiceContract::class)->toggleLikeOf($this, LikeType::DISLIKE, $userId);
    }

    /**
     * Has the user already disliked likeable model.
     *
     * @param int|null $userId
     * @return bool
     */
    public function disliked($userId = null)
    {
        return app(LikeableServiceContract::class)->isLiked($this, LikeType::DISLIKE, $userId);
    }

    /**
     * Delete dislikes related to the current record.
     *
     * @return void
     */
    public function removeDislikes()
    {
        app(LikeableServiceContract::class)->removeModelLikes($this, LikeType::DISLIKE);
    }
}
