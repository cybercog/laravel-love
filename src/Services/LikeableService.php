<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Services;

use Cog\Likeable\Contracts\HasLikes as HasLikesContract;
use Cog\Likeable\Contracts\Like as LikeContract;
use Cog\Likeable\Contracts\LikeableService as LikeableServiceContract;
use Cog\Likeable\Contracts\LikeCounter as LikeCounterContract;
use Cog\Likeable\Enums\LikeType;
use Cog\Likeable\Exceptions\LikerNotDefinedException;
use Cog\Likeable\Exceptions\LikeTypeInvalidException;
use DB;

/**
 * Class LikeableService.
 *
 * @package Cog\Likeable\Services
 */
class LikeableService implements LikeableServiceContract
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
    public function addLikeTo(HasLikesContract $model, $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        $like = $model->likesAndDislikes()->where([
            'user_id' => $userId,
        ])->first();

        if (!$like) {
            $model->likes()->create([
                'user_id' => $userId,
                'type_id' => $this->getLikeTypeId($type),
            ]);

            return;
        }

        if ($like->type_id == $this->getLikeTypeId($type)) {
            return;
        }

        $like->delete();

        $model->likes()->create([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ]);
    }

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
    public function removeLikeFrom(HasLikesContract $model, $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        $like = $model->likesAndDislikes()->where([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ])->first();

        if (!$like) {
            return;
        }

        $like->delete();
    }

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
    public function toggleLikeOf(HasLikesContract $model, $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        $like = $model->likesAndDislikes()->where([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ])->exists();

        if ($like) {
            $this->removeLikeFrom($model, $type, $userId);
        } else {
            $this->addLikeTo($model, $type, $userId);
        }
    }

    /**
     * Has the user already liked likeable model.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @param string $type
     * @param int|null $userId
     * @return bool
     */
    public function isLiked(HasLikesContract $model, $type, $userId)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if (!$userId) {
            return false;
        }

        return $model->likesAndDislikes()->where([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ])->exists();
    }

    /**
     * Decrement the total like count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function decrementLikeCount(HasLikesContract $model)
    {
        $counter = $model->likesCounter()->first();

        if (!$counter) {
            return;
        }

        $counter->decrement('count');
    }

    /**
     * Increment the total like count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function incrementLikeCount(HasLikesContract $model)
    {
        $counter = $model->likesCounter()->first();

        if (!$counter) {
            $counter = $model->likesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::LIKE,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function decrementDislikeCount(HasLikesContract $model)
    {
        $counter = $model->dislikesCounter()->first();

        if (!$counter) {
            return;
        }

        $counter->decrement('count');
    }

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @return void
     */
    public function incrementDislikeCount(HasLikesContract $model)
    {
        $counter = $model->dislikesCounter()->first();

        if (!$counter) {
            $counter = $model->dislikesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::DISLIKE,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Remove like counters by likeable type.
     *
     * @param string $likeableType
     * @param string|null $type
     * @return void
     */
    public function removeLikeCountersOfType($likeableType, $type = null)
    {
        if (class_exists($likeableType)) {
            $model = new $likeableType;
            $likeableType = $model->getMorphClass();
        }

        $counters = app(LikeCounterContract::class)->where('likeable_type', $likeableType);
        if (!is_null($type)) {
            $counters->where('type_id', $this->getLikeTypeId($type));
        }
        $counters->delete();
    }

    /**
     * Remove all likes from likeable model.
     *
     * @param \Cog\Likeable\Contracts\HasLikes $model
     * @param string $type
     * @return void
     */
    public function removeModelLikes(HasLikesContract $model, $type)
    {
        app(LikeContract::class)->where([
            'likeable_id' => $model->getKey(),
            'likeable_type' => $model->getMorphClass(),
            'type_id' => $this->getLikeTypeId($type),
        ])->delete();

        app(LikeCounterContract::class)->where([
            'likeable_id' => $model->getKey(),
            'likeable_type' => $model->getMorphClass(),
            'type_id' => $this->getLikeTypeId($type),
        ])->delete();
    }

    /**
     * Fetch records that are liked by a given user id.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $type
     * @param int|null $userId
     * @return \Illuminate\Database\Query\Builder
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    public function scopeWhereLikedBy($query, $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        return $query->whereHas('likesAndDislikes', function ($q) use ($type, $userId) {
            $q->where([
                'user_id' => $userId,
                'type_id' => $this->getLikeTypeId($type),
            ]);
        });
    }

    /**
     * Fetch likes counters data.
     *
     * @param string $likeableType
     * @param string $likeType
     * @return array
     */
    public function fetchLikesCounters($likeableType, $likeType)
    {
        $likesCount = app(LikeContract::class)->query()
            ->select([
                DB::raw('COUNT(*) AS count'),
                'likeable_type',
                'likeable_id',
                'type_id',
            ])
            ->where('likeable_type', $likeableType);

        if (!is_null($likeType)) {
            $likesCount->where('type_id', $this->getLikeTypeId($likeType));
        }

        $likesCount->groupBy('likeable_id', 'type_id');

        $counters = $likesCount->get()->toArray();

        return $counters;
    }

    /**
     * Get current user id or get user id passed in.
     *
     * @param int $userId
     * @return int
     *
     * @throws \Cog\Likeable\Exceptions\LikerNotDefinedException
     */
    protected function getLikerUserId($userId)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if (!$userId) {
            throw new LikerNotDefinedException();
        }

        return $userId;
    }

    /**
     * Fetch the primary ID of the currently logged in user.
     *
     * @return int
     */
    protected function loggedInUserId()
    {
        return auth()->id();
    }

    /**
     * Get like type id from name.
     *
     * @param string $type
     * @return int
     *
     * @throws \Cog\Likeable\Exceptions\LikeTypeInvalidException
     */
    protected function getLikeTypeId($type)
    {
        $type = strtoupper($type);
        if (!defined("\\Cog\\Likeable\\Enums\\LikeType::{$type}")) {
            throw new LikeTypeInvalidException("Like type `{$type}` not exist");
        }

        return constant("\\Cog\\Likeable\\Enums\\LikeType::{$type}");
    }
}
