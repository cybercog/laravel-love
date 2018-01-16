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

namespace Cog\Laravel\Love\Likeable\Models\Traits;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Contracts\Love\LikeCounter\Models\LikeCounter as LikeCounterContract;
use Cog\Contracts\Love\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Laravel\Love\Like\Enums\LikeType;
use Cog\Laravel\Love\Likeable\Observers\LikeableObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

/**
 * Trait Likeable.
 *
 * @package Cog\Laravel\Love\Likeable\Models\Traits
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
        static::observe(LikeableObserver::class);
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
    public function getLikesCountAttribute(): int
    {
        return $this->likesCounter ? $this->likesCounter->count : 0;
    }

    /**
     * Model dislikesCount attribute.
     *
     * @return int
     */
    public function getDislikesCountAttribute(): int
    {
        return $this->dislikesCounter ? $this->dislikesCounter->count : 0;
    }

    /**
     * Did the currently logged in user like this model.
     *
     * @return bool
     */
    public function getLikedAttribute(): bool
    {
        return $this->isLikedBy();
    }

    /**
     * Did the currently logged in user dislike this model.
     *
     * @return bool
     */
    public function getDislikedAttribute(): bool
    {
        return $this->isDislikedBy();
    }

    /**
     * Difference between likes and dislikes count.
     *
     * @return int
     */
    public function getLikesDiffDislikesCountAttribute(): int
    {
        return $this->likesCount - $this->dislikesCount;
    }

    /**
     * Add a like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function likeBy($userId = null)
    {
        app(LikeableServiceContract::class)->addLikeTo($this, LikeType::LIKE, $userId);
    }

    /**
     * Add a dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function dislikeBy($userId = null)
    {
        app(LikeableServiceContract::class)->addLikeTo($this, LikeType::DISLIKE, $userId);
    }

    /**
     * Remove a like from this record for the given user.
     *
     * @param null|int $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function unlikeBy($userId = null)
    {
        app(LikeableServiceContract::class)->removeLikeFrom($this, LikeType::LIKE, $userId);
    }

    /**
     * Remove a dislike from this record for the given user.
     *
     * @param null|int $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function undislikeBy($userId = null)
    {
        app(LikeableServiceContract::class)->removeLikeFrom($this, LikeType::DISLIKE, $userId);
    }

    /**
     * Toggle like for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function toggleLikeBy($userId = null)
    {
        app(LikeableServiceContract::class)->toggleLikeOf($this, LikeType::LIKE, $userId);
    }

    /**
     * Toggle dislike for model by the given user.
     *
     * @param mixed $userId If null will use currently logged in user.
     * @return void
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function toggleDislikeBy($userId = null)
    {
        app(LikeableServiceContract::class)->toggleLikeOf($this, LikeType::DISLIKE, $userId);
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
     * Delete dislikes related to the current record.
     *
     * @return void
     */
    public function removeDislikes()
    {
        app(LikeableServiceContract::class)->removeModelLikes($this, LikeType::DISLIKE);
    }

    /**
     * Has the user already liked likeable model.
     *
     * @param null|int $userId
     * @return bool
     */
    public function isLikedBy($userId = null): bool
    {
        return app(LikeableServiceContract::class)->isLiked($this, LikeType::LIKE, $userId);
    }

    /**
     * Has the user already disliked likeable model.
     *
     * @param null|int $userId
     * @return bool
     */
    public function isDislikedBy($userId = null): bool
    {
        return app(LikeableServiceContract::class)->isLiked($this, LikeType::DISLIKE, $userId);
    }

    /**
     * Fetch records that are liked by a given user id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param null|int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function scopeWhereLikedBy(Builder $query, $userId = null): Builder
    {
        return $this->applyScopeWhereLikedBy($query, LikeType::LIKE, $userId);
    }

    /**
     * Fetch records that are disliked by a given user id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param null|int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function scopeWhereDislikedBy(Builder $query, $userId = null): Builder
    {
        return $this->applyScopeWhereLikedBy($query, LikeType::DISLIKE, $userId);
    }

    /**
     * Fetch records sorted by likes count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByLikesCount(Builder $query, string $direction = 'desc'): Builder
    {
        return $this->applyScopeOrderByLikesCount($query, LikeType::LIKE, $direction);
    }

    /**
     * Fetch records sorted by likes count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByDislikesCount(Builder $query, string $direction = 'desc'): Builder
    {
        return $this->applyScopeOrderByLikesCount($query, LikeType::DISLIKE, $direction);
    }

    /**
     * Fetch records that are liked by a given user id.
     *
     * @todo think about method name
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @param null|int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    private function applyScopeWhereLikedBy(Builder $query, string $type, $userId): Builder
    {
        $service = app(LikeableServiceContract::class);
        $userId = $service->getLikerUserId($userId);
        $typeId = $service->getLikeTypeId($type);

        return $query->whereHas('likesAndDislikes', function (Builder $innerQuery) use ($typeId, $userId) {
            $innerQuery->where('user_id', $userId);
            $innerQuery->where('type_id', $typeId);
        });
    }

    /**
     * Fetch records sorted by likes count.
     *
     * @todo think about method name
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $likeType
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyScopeOrderByLikesCount(Builder $query, string $likeType, string $direction): Builder
    {
        $likeable = $query->getModel();
        $typeId = app(LikeableServiceContract::class)->getLikeTypeId($likeType);

        return $query
            ->select($likeable->getTable() . '.*', 'love_like_counters.count')
            ->leftJoin('love_like_counters', function (JoinClause $join) use ($likeable, $typeId) {
                $join
                    ->on('love_like_counters.likeable_id', '=', "{$likeable->getTable()}.{$likeable->getKeyName()}")
                    ->where('love_like_counters.likeable_type', '=', $likeable->getMorphClass())
                    ->where('love_like_counters.type_id', '=', $typeId);
            })
            ->orderBy('love_like_counters.count', $direction);
    }
}
