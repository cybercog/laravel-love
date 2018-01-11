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

namespace Cog\Laravel\Love\Likeable\Services;

use Cog\Contracts\Love\Like\Exceptions\InvalidLikeType;
use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
use Cog\Contracts\Love\LikeCounter\Models\LikeCounter as LikeCounterContract;
use Cog\Contracts\Love\Liker\Exceptions\InvalidLiker;
use Cog\Contracts\Love\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Contracts\Love\Liker\Models\Liker as LikerContract;
use Cog\Laravel\Love\Like\Enums\LikeType;
use Illuminate\Support\Facades\DB;

/**
 * Class LikeableService.
 *
 * @package Cog\Laravel\Love\Likeable\Services
 */
class LikeableService implements LikeableServiceContract
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
    public function addLikeTo(LikeableContract $likeable, $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        $like = $likeable->likesAndDislikes()->where([
            'user_id' => $userId,
        ])->first();

        if (!$like) {
            $likeable->likes()->create([
                'user_id' => $userId,
                'type_id' => $this->getLikeTypeId($type),
            ]);

            return;
        }

        if ($like->type_id == $this->getLikeTypeId($type)) {
            return;
        }

        $like->delete();

        $likeable->likes()->create([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ]);
    }

    /**
     * Remove a like to likeable model by user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $type
     * @param null|int $userId
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function removeLikeFrom(LikeableContract $likeable, $type, $userId)
    {
        $like = $likeable->likesAndDislikes()->where([
            'user_id' => $this->getLikerUserId($userId),
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
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $type
     * @param string $userId
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function toggleLikeOf(LikeableContract $likeable, $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        $like = $likeable->likesAndDislikes()->where([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ])->exists();

        if ($like) {
            $this->removeLikeFrom($likeable, $type, $userId);
        } else {
            $this->addLikeTo($likeable, $type, $userId);
        }
    }

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
    public function isLiked(LikeableContract $likeable, $type, $userId): bool
    {
        // TODO: (?) Use `$userId = $this->getLikerUserId($userId);`
        if ($userId instanceof LikerContract) {
            $userId = $userId->getKey();
        }

        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if (!$userId) {
            return false;
        }

        $typeId = $this->getLikeTypeId($type);

        $exists = $this->hasLikeOrDislikeInLoadedRelation($likeable, $typeId, $userId);
        if (!is_null($exists)) {
            return $exists;
        }

        return $likeable->likesAndDislikes()->where([
            'user_id' => $userId,
            'type_id' => $typeId,
        ])->exists();
    }

    /**
     * Decrement the total like count stored in the counter.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function decrementLikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->likesCounter()->first();

        if (!$counter) {
            return;
        }

        $counter->decrement('count');
    }

    /**
     * Increment the total like count stored in the counter.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function incrementLikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->likesCounter()->first();

        if (!$counter) {
            $counter = $likeable->likesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::LIKE,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function decrementDislikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->dislikesCounter()->first();

        if (!$counter) {
            return;
        }

        $counter->decrement('count');
    }

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return void
     */
    public function incrementDislikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->dislikesCounter()->first();

        if (!$counter) {
            $counter = $likeable->dislikesCounter()->create([
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
     * @param null|string $type
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function removeLikeCountersOfType($likeableType, $type = null)
    {
        if (class_exists($likeableType)) {
            /** @var \Cog\Contracts\Love\Likeable\Models\Likeable $likeable */
            $likeable = new $likeableType;
            $likeableType = $likeable->getMorphClass();
        }

        /** @var \Illuminate\Database\Eloquent\Builder $counters */
        $counters = app(LikeCounterContract::class)->where('likeable_type', $likeableType);
        if (!is_null($type)) {
            $counters->where('type_id', $this->getLikeTypeId($type));
        }
        $counters->delete();
    }

    /**
     * Remove all likes from likeable model.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $type
     * @return void
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function removeModelLikes(LikeableContract $likeable, $type)
    {
        app(LikeContract::class)->where([
            'likeable_id' => $likeable->getKey(),
            'likeable_type' => $likeable->getMorphClass(),
            'type_id' => $this->getLikeTypeId($type),
        ])->delete();

        app(LikeCounterContract::class)->where([
            'likeable_id' => $likeable->getKey(),
            'likeable_type' => $likeable->getMorphClass(),
            'type_id' => $this->getLikeTypeId($type),
        ])->delete();
    }

    /**
     * Get collection of users who liked entity.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return \Illuminate\Support\Collection
     */
    public function collectLikersOf(LikeableContract $likeable)
    {
        $userModel = $this->resolveUserModel();

        $likersIds = $likeable->likes->pluck('user_id');

        return $userModel::whereKey($likersIds)->get();
    }

    /**
     * Get collection of users who disliked entity.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @return \Illuminate\Support\Collection
     */
    public function collectDislikersOf(LikeableContract $likeable)
    {
        $userModel = $this->resolveUserModel();

        $likersIds = $likeable->dislikes->pluck('user_id');

        return $userModel::whereKey($likersIds)->get();
    }

    /**
     * Fetch likes counters data.
     *
     * @param string $likeableType
     * @param string $likeType
     * @return array
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function fetchLikesCounters($likeableType, $likeType): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder $likesCount */
        $likesCount = app(LikeContract::class)
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

        return $likesCount->get()->toArray();
    }

    /**
     * Get current user id or get user id passed in.
     *
     * @todo Can we make it protected or move it out from likeable service?
     * @param int $userId
     * @return int
     *
     * @throws \Cog\Contracts\Love\Liker\Exceptions\InvalidLiker
     */
    public function getLikerUserId($userId): int
    {
        if ($userId instanceof LikerContract) {
            return $userId->getKey();
        }

        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if (!$userId) {
            throw InvalidLiker::notDefined();
        }

        return $userId;
    }

    /**
     * Fetch the primary ID of the currently logged in user.
     *
     * @return null|int
     */
    protected function loggedInUserId()
    {
        return auth()->id();
    }

    /**
     * Get like type id from name.
     *
     * @todo move to Enum class
     * @param string $type
     * @return string
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    public function getLikeTypeId($type)
    {
        $type = strtoupper($type);
        if (!defined("\\Cog\\Laravel\\Love\\Like\\Enums\\LikeType::{$type}")) {
            throw InvalidLikeType::notExists($type);
        }

        return constant("\\Cog\\Laravel\\Love\\Like\\Enums\\LikeType::{$type}");
    }

    /**
     * Retrieve User's model class name.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    private function resolveUserModel()
    {
        return config('auth.providers.users.model');
    }

    /**
     * Search in eager loaded relations if model was liked/disliked by user.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param string $typeId
     * @param int $userId
     * @return null|bool
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    private function hasLikeOrDislikeInLoadedRelation(LikeableContract $likeable, $typeId, $userId)
    {
        $relations = $this->likeTypeRelations($typeId);

        foreach ($relations as $relation) {
            if (!$likeable->relationLoaded($relation)) {
                continue;
            }

            return $likeable->{$relation}->contains(function ($item) use ($userId, $typeId) {
                return $item->user_id == $userId && $item->type_id === $typeId;
            });
        }

        return null;
    }

    /**
     * Resolve list of likeable relations by like type.
     *
     * @param string $type
     * @return array
     *
     * @throws \Cog\Contracts\Love\Like\Exceptions\InvalidLikeType
     */
    private function likeTypeRelations($type)
    {
        $relations = [
            LikeType::LIKE => [
                'likes',
                'likesAndDislikes',
            ],
            LikeType::DISLIKE => [
                'dislikes',
                'likesAndDislikes',
            ],
        ];

        if (!isset($relations[$type])) {
            throw InvalidLikeType::notExists($type);
        }

        return $relations[$type];
    }
}
