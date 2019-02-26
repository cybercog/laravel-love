# Changelog

All notable changes to `laravel-love` will be documented in this file.

## [6.1.0] - 2019-02-26

### Added

- Laravel 5.8 support

### Changed

- `ReactionCounter` updates on background using queue
- `ReactionTotal` updates on background using queue

## [6.0.0] - 2019-02-25

Package API was refactored from a scratch.
Code has a lot of breaking changes and cannot be updated easily.

Follow [upgrade instructions](UPGRADING.md#from-v5-to-v6) to migrate database to new structure.

### Added

- `ReactionType` model
- `Reacter` model
- `Reactant` model
- `ReactionTotal` model
- `ReactionHasBeenAdded` event
- `ReactionHasBeenRemoved` event
- `love_reacters` database table was added
- `love_reactants` database table was added
- `love_reaction_types` database table was added
- `love_reactant_reaction_totals` database table was added

### Changed

- `Liker` trait replaced with `Reacterable`
- `Likeable` trait replaced with `Reactable`
- `LikeCounter` model replaced with `ReactionCounter`
- `love_likes` database table was replaced with `love_reactions`
- `love_like_counters` database table was replaced with `love_reactant_reaction_counters`

### Removed

- Laravel 5.5 support obsolete
- PHP < 7.1.3 support obsolete
- `LikeableWasLiked` event was removed
- `LikeableWasDisliked` event was removed
- `LikeableWasUnliked` event was removed
- `LikeableWasUndisliked` event was removed

## [5.2.0] - 2018-09-09

### Added

- ([#21](https://github.com/cybercog/laravel-love/pull/21)) Laravel 5.7 support

## [5.1.1] - 2018-02-16

### Fixed

- ([#11](https://github.com/cybercog/laravel-love/pull/11)) Added missing migrations auto-loading

## [5.1.0] - 2018-02-08

### Added

- ([#9](https://github.com/cybercog/laravel-love/pull/9)) Laravel 5.6 support

## [5.0.0] - 2018-01-16

### Added

- Added `Cog\Contracts\Love\Liker\Models\Liker` contract with methods `like`, `dislike`, `unlike`, `undislike`, `toggleLike`, `toggleDislike`, `hasLiked`, `hasDisliked`

### Changed

- Method `like` renamed to `likeBy` in `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Method `dislike` renamed to `dislikeBy` in `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Method `unlike` renamed to `unlikeBy` in `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Method `undislike` renamed to `undislikeBy` in `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Method `liked` renamed to `likedBy` in `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Method `disliked` renamed to `dislikedBy` in `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Method `likeToggle` renamed to `toggleLikeBy` in `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Method `dislikeToggle` renamed to `toggleDislikeBy` in `Cog\Contracts\Love\Likeable\Models\Likeable` contract

## [4.0.0] - 2018-01-04

### Changed

- Console command `LikeableRecount` moved from `Cog\Likeable\Console` to `Cog\Laravel\Love\Console\Commands` namespace
- Console command `LikeableRecount` renamed to `Recount`
- Contracts moved from `Cog\Likeable\Contracts` to `Cog\Contracts\Love` namespace
- Database table `like` renamed to `love_likes`
- Database table `like_counter` renamed to `love_like_counters`
- Database table column `updated_at` was added to `love_likes` table 
- Database table columns `created_at` & `updated_at` were added to `love_like_counters` table
- Events were moved from `Cog\Likeable\Events` to `Cog\Laravel\Love\Likeable\Events` namespace
- Event `ModelWasDisliked` renamed to `LikeableWasDisliked`
- Event `ModelWasLiked` renamed to `LikeableWasLiked`
- Event `ModelWasUndisliked` renamed to `LikeableWasUndisliked`
- Event `ModelWasUnliked` renamed to `LikeableWasUnliked`
- All Likeable's events public property `$model` was renamed to `$likeable`
- Constant values `LikeType::LIKE` & `LikeType::DISLIKE` are uppercase now and equal to `LIKE` & `DISLIKE` respectively
- Exceptions extends `\RuntimeException` instead of `\Exception`
- Exception `LikeTypeInvalidException` moved from `Cog\Likeable\Contracts\Exceptions` to `Cog\Contracts\Love\Like\Exceptions`
- Exception `LikeTypeInvalidException` renamed to `InvalidLikeType`
- Exception `LikerNotDefinedException` moved from `Cog\Likeable\Contracts\Exceptions` to `Cog\Contracts\Love\Liker\Exceptions`
- Exception `LikerNotDefinedException` renamed to `InvalidLiker`
- Exception `ModelInvalidException` moved from `Cog\Likeable\Contracts\Exceptions` to `Cog\Contracts\Love\Likeable\Exceptions`
- Exception `ModelInvalidException` renamed to `InvalidLikeable`
- Observer class `ModelObserver` moved from `Cog\Likeable\Observers` to `Cog\Laravel\Love\Likeable\Observers` namespace
- Observer class `ModelObserver` renamed to `LikeableObserver`
- Service Provider `LikableServiceProvider` was moved from `Cog\Likeable\Providers` to `Cog\Laravel\Love\Providers` namespace
- Service Provider `LikableServiceProvider` was renamed to `LoveServiceProvider`

### Removed

- Removed deprecated `Cog\Likeable\Contracts\HasLikes` contract
- Removed deprecated `Cog\Likeable\Traits\HasLikes` trait
- Removed `scopeWhereLikedBy` method from `Cog\Contracts\Love\Likeable\Services\LikeableService` contract
- Removed `scopeWhereLikedBy` method from `Cog\Laravel\Love\Likeable\Services\LikeableService` class
- Removed `scopeOrderByLikesCount` method from `Cog\Contracts\Love\Likeable\Services\LikeableService` contract
- Removed `scopeOrderByLikesCount` method from `Cog\Laravel\Love\Likeable\Services\LikeableService` class
- Removed `getLikesCountAttribute` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Removed `getDislikesCountAttribute` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Removed `getLikedAttribute` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Removed `getDislikedAttribute` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Removed `getLikesDiffDislikesCountAttribute` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Removed `scopeWhereLikedBy` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Removed `scopeWhereDislikedBy` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Removed `scopeOrderByLikesCount` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract
- Removed `scopeOrderByDislikesCount` method from `Cog\Contracts\Love\Likeable\Models\Likeable` contract

## [3.1.0] - 2017-12-28

### Changed

- Checks if model liked by user will try to search in eager loaded relations first

## [3.0.0] - 2017-08-24

### Added

- Laravel 5.5 support
- Laravel Package Auto-Discovery support
- Eloquent related method `getKey` & `getMorphClass` added to `Cog\Likeable\Contracts\Likeable` contract
- `collectLikers`, `collectDislikers` & `scopeOrderByDislikesCount` methods added to `Cog\Likeable\Contracts\Likeable` contract
- `collectLikersOf` & `collectDislikersOf` methods to `Cog\Likeable\Contracts\LikeableService` contract

### Changed

- `Cog\Likeable\Contracts` contract renamed to `Cog\Likeable\Contracts\Likeable`
- `Cog\Likeable\Traits\HasLikes` trait renamed to `Cog\Likeable\Traits\Likeable`

## [2.2.5] - 2017-07-10

### Fixed

- Event observing of custom `Like` model (#18)

## [2.2.4] - 2017-04-20

### Added

- `orderByDislikesCount` scope added to `HasLikes` trait
- `scopeOrderByLikesCount` method to `LikeableService`

### Fixed

- `orderByLikesCount` count only likes now

## [2.2.3] - 2017-04-20

### Fixed

`orderByLikesCount` work in MySQL databases

## [2.2.2] - 2017-04-09

### Fixed

- `orderByLikesCount` returns models without likes too

## [2.2.1] - 2017-04-09

### Fixed

- `orderByLikesCount` database query fixed

## [2.2.0] - 2017-04-09

### Added

- `Article::orderByLikesCount('asc')` scope for the model. Uses `desc` as default order direction

## [2.1.0] - 2017-02-20

### Added

- Laravel 5.4 support.

## [2.0.1] - 2017-01-11

- Removed unused properties in `LikeObserver` (#12)
- Foreign key in migration commented out (#11)

## [2.0.0] - 2016-09-11

- Renamed `FollowableService` methods to follow code style consistency:
    - `incrementLikeCount()` to `incrementLikesCount()`
    - `decrementLikeCount()` to `decrementLikesCount()`
    - `decrementDislikeCount()` to `decrementDislikesCount()`
    - `incrementDislikeCount()` to `incrementDislikesCount()`

## [1.1.2] - 2016-09-07

- Fix enum like types

## [1.1.1] - 2016-09-07

- Fix likeable enums database default value

## [1.1.0] - 2016-09-07

- Renamed `HasLikes` trait methods to follow code style consistency:
    - `likeCounter()` to `likesCounter()`
    - `dislikeCounter()` to `dislikesCounter()`

## 1.0.0 - 2016-09-06

- Initial release

[6.1.0]: https://github.com/cybercog/laravel-love/compare/v6.0.0...v6.1.0
[6.0.0]: https://github.com/cybercog/laravel-love/compare/5.2.0...v6.0.0
[5.2.0]: https://github.com/cybercog/laravel-love/compare/5.1.1...5.2.0
[5.1.1]: https://github.com/cybercog/laravel-love/compare/5.1.0...5.1.1
[5.1.0]: https://github.com/cybercog/laravel-love/compare/5.0.0...5.1.0
[5.0.0]: https://github.com/cybercog/laravel-love/compare/4.0.0...5.0.0
[4.0.0]: https://github.com/cybercog/laravel-love/compare/3.1.0...4.0.0
[3.1.0]: https://github.com/cybercog/laravel-love/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/cybercog/laravel-love/compare/2.2.5...3.0.0
[2.2.5]: https://github.com/cybercog/laravel-love/compare/2.2.4...2.2.5
[2.2.4]: https://github.com/cybercog/laravel-love/compare/2.2.3...2.2.4
[2.2.3]: https://github.com/cybercog/laravel-love/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/cybercog/laravel-love/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/cybercog/laravel-love/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/cybercog/laravel-love/compare/2.1.0...2.2.0
[2.1.0]: https://github.com/cybercog/laravel-love/compare/2.0.1...2.1.0
[2.0.1]: https://github.com/cybercog/laravel-love/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/cybercog/laravel-love/compare/1.1.2...2.0.0
[1.1.2]: https://github.com/cybercog/laravel-love/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/cybercog/laravel-love/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/cybercog/laravel-love/compare/1.0.0...1.1.0
