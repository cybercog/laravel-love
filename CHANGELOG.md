# Changelog

All notable changes to `laravel-love` will be documented in this file.

## [Unreleased]

## [8.9.0] - 2022-02-23

### Added

- ([#206]) Added Laravel 9 support

## [8.8.1] - 2021-04-03

### Removed

- ([#197]) Dropped PHP 7.1 support

## [8.8.0] - 2021-04-03

### Removed

- ([#196]) Dropped Laravel 5.7 support
- ([#196]) Dropped Laravel 5.8 support

### Fixed

- ([#196]) Fixed CVE-2021-21263 vulnerability
- ([#196]) Fixed GHSA-x7p5-p2c9-phvg vulnerability

## [8.7.1] - 2020-12-06

### Fixed

- ([#186]) Improve CLI application performance by replacing `$name` with `$defaultName` static property in commands
- ([#187]) Fixed inconsistency in method parameter names

## [8.7.0] - 2020-12-06

### Added

- ([#185]) Added PHP 8.x support

## [8.6.1] - 2020-10-04

### Changed

- ([#178]) Rename imported interfaces aliases

## [8.6.0] - 2020-10-02

### Added

- ([#177]) Added accessor methods to aggregates jobs

## [8.5.0] - 2020-09-09

### Added

- ([#176]) Added Laravel 8 support

## [8.4.0] - 2020-05-22

### Added

- ([#165]) Added table names configuration

### Fixed

- ([#161]) Removed redundant queues from Reactant listeners

## [8.3.1] - 2020-03-06

### Added

- ([#158]) Add Laravel 7.x support

## [8.3.0] - 2020-02-16

### Added

- ([#146]) Extracted logic from `Cog\Laravel\Love\Reactant\Listeners\IncrementAggregates` listener to `Cog\Laravel\Love\Reactant\Jobs\IncrementReactionAggregatesJob`
- ([#146]) Extracted logic from `Cog\Laravel\Love\Reactant\Listeners\DecrementAggregates` listener to `Cog\Laravel\Love\Reactant\Jobs\DecrementReactionAggregatesJob`
- ([#147]) Extracted event listeners registering from `Cog\Laravel\Love\LoveServiceProvider` to `Cog\Laravel\Love\LoveEventServiceProvider`
- ([#148]) Extracted rebuild of reactant reactions counters from `Cog\Laravel\Love\Console\Commands\Recount` command to `Cog\Laravel\Love\Reactant\Jobs\RebuildReactionAggregatesJob`
- ([#148]) Added `--queue-connection=` option to `love:recount` Artisan command

### Fixed

- ([#148]) Fixed `love:recount` Artisan command execution when `love_reactant_reaction_totals` database table is empty
- ([#151]) Fixed `love:recount` Artisan command return type

## [8.2.0] - 2020-01-30

### Added

- ([#127]) Artisan commands for registering existing models as reactants/reacters

## [8.1.2] - 2019-09-22

### Fixed

- ([#121]) Drop foreign key before database column delete

## [8.1.1] - 2019-09-13

### Fixed

- ([#118]) Fix custom connection in database migrations

## [8.1.0] - 2019-09-03

### Added

- ([#113]) Laravel 6 support

### Changed

- ([#110]) Removed dependency of `RateOutOfRange` exception in contracts namespace on concrete `Reaction` model implementation
- ([#110]) Renamed `withValue` method to `withValueBetween` in `RateOutOfRange` exception
- ([#110]) Added `$minimumRate` parameter to `withValueBetween` method in `RateOutOfRange` exception
- ([#110]) Added `$maximumRate` parameter to `withValueBetween` method in `RateOutOfRange` exception
- ([#111]) Changed `$rate` parameter type from `float` to `?float` of `hasReactedTo` method in `Reacter` model contract
- ([#111]) Changed `$rate` parameter type from `float` to `?float` of `hasNotReactedTo` method in `Reacter` model contract

## [5.3.0] - 2019-09-03

### Added

- ([#114]) Laravel 6 support

## [8.0.0] - 2019-08-08

Code has a lot of breaking changes because of new Weighted Reaction System.

Follow [upgrade instructions](UPGRADING.md#from-v7-to-v8) to migrate code & database to new structure.

### Added

- Added `love:upgrade-v7-to-v8` Artisan command
- ([#90]) Added `ReactionCounter::COUNT_DEFAULT` public constant
- ([#90]) Added `ReactionCounter::WEIGHT_DEFAULT` public constant
- ([#90]) Added `ReactionTotal::COUNT_DEFAULT` public constant
- ([#90]) Added `ReactionTotal::WEIGHT_DEFAULT` public constant
- ([#91]) Added `Reaction::RATE_DEFAULT` public constant
- ([#91]) Added `Reaction::RATE_MIN` public constant
- ([#91]) Added `Reaction::RATE_MAX` public constant
- ([#91]) Added `ReactionType::MASS_DEFAULT` public constant
- ([#91]) Added `rate` attribute to `Reacter` model
- ([#91]) Added `rate DECIMIAL(4, 2)` column to `love_reactions` db table
- ([#91]) Added ability to call `Reacter::reactTo` with already reacted reactant, same reaction type, but only `rate` differs
- ([#91]) Added `Cog\Contracts\Love\Reaction\Exceptions\RateOutOfRange` exception
- ([#100]) Added `Cog\Contracts\Love\Reaction\Exceptions\RateInvalid` exception
- ([#96]) Added progress bar to `love:recount` Artisan command
- ([#97]) Added ability to call `Reactable::joinReactionCounterOfType` more than once
- ([#102]) Added `scopeWhereNotReactedBy` scope to `Reactable` model trait

### Changed

- ([#79]) Renamed `isReactedTo` method to `hasReactedTo` in `Reacter` model contract
- ([#79]) Added `$reactionType` parameter to `hasReactedTo` in `Reacter` model contract
- ([#91]) Added `$rate` parameter to `hasReactedTo` method in `Reacter` model contract
- ([#91]) Added `$rate` parameter to `hasReactedTo` method in `Reacter` facade contract
- ([#79]) Renamed `isNotReactedTo` method to `hasNotReactedTo` in `Reacter` model contract
- ([#79]) Added `$reactionType` parameter to `hasNotReactedTo` in `Reacter` model contract
- ([#91]) Added `$rate` parameter to `hasNotReactedTo` method in `Reacter` model contract
- ([#91]) Added `$rate` parameter to `hasNotReactedTo` method in `Reacter` facade contract
- ([#79]) Added `$reactionType` parameter to `isReactedBy` in `Reactant` model contract
- ([#91]) Added `$rate` parameter to `isReactedBy` method in `Reactant` model contract
- ([#91]) Added `$rate` parameter to `isReactedBy` method in `Reactant` facade contract
- ([#79]) Added `$reactionType` parameter to `isNotReactedBy` in `Reactant` model contract
- ([#91]) Added `$rate` parameter to `isNotReactedBy` method in `Reactant` model contract
- ([#91]) Added `$rate` parameter to `isNotReactedBy` method in `Reactant` facade contract
- ([#83]) Artisan command `love:reaction-type-add` awaits options instead of arguments
- ([#87]) Resolving models default attributes values moved from accessors to Eloquent methods
- ([#88]) Renamed `weight` attribute to `mass` in `ReactionType` model
- ([#88]) Renamed `getWeight` method to `getMass` in `ReactionType` model contract
- ([#89]) Added `$reactionType` parameter to `scopeWhereReactedBy` method in `Reactable` model trait
- ([#90]) Moved `count` & `weight` attributes default values of `ReactionCounter` to application level
- ([#90]) Moved `count` & `weight` attributes default values of `ReactionTotal` to application level
- ([#91]) Changed `getWeight` method return type from `int` to `float` in reactant's `ReactionCounter` model contract
- ([#91]) Changed `$amount` parameter type from `int` to `float` of `incrementWeight` method in reactant's `ReactionCounter` model contract
- ([#91]) Changed `$amount` parameter type from `int` to `float` of `decrementWeight` method in reactant's `ReactionCounter` model contract
- ([#91]) Changed `getWeight` method return type from `int` to `float` in reactant's `ReactionTotal` model contract
- ([#91]) Changed `$amount` parameter type from `int` to `float` of `incrementWeight` method in reactant's `ReactionTotal` model contract
- ([#91]) Changed `$amount` parameter type from `int` to `float` of `decrementWeight` method in reactant's `ReactionTotal` model contract
- ([#91]) Added `?float $rate` parameter to `reactTo` method in `Reacter` facade contract
- ([#91]) Added `?float $rate` parameter to `reactTo` method in `Reacter` model contract
- ([#91]) Added `getRate` method to `Reaction` model contract
- ([#91]) Changed `getWeight` method return type from `int` to `float` in `Reaction` model contract
- ([#91]) Changed `weight` column type to `DECIMIAL(13, 2)` in `love_reactant_reaction_counters` db table
- ([#91]) Changed `weight` column type to `DECIMIAL(13, 2)` in `love_reactant_reaction_totals` db table
- ([#96]) Changed signature of `love:recount` Artisan command to `love:recount {--model=} {--type=}`
- ([#99]) Make `Reacterable` parameter nullable in `isReactedBy` method of `Reactant` facade contract
- ([#99]) Make `Reacterable` parameter nullable in `isNotReactedBy` method of `Reactant` facade contract
- ([#102]) Changed second parameter type from `Reactant` to `Reacterable` in `scopeWhereReactedBy` method of `Reactable` model trait
- ([#102]) Changed third parameter type from `?ReactionType` to `?string` in `scopeWhereReactedBy` method of `Reactable` model trait
- ([#97]) Added third `?string $alias` parameter to `scopeJoinReactionCounterOfType` method of `Reactable` model trait
- ([#102]) Added second `?string $alias` parameter to `scopeJoinReactionTotal` method of `Reactable` model trait
- ([#102]) Renamed virtual column `reactions_count` to `reaction_{$type}_count` in `scopeJoinReactionCounterOfType` method of `Reactable` model trait
- ([#102]) Renamed virtual column `reactions_weight` to `reaction_{$type}_weight` in `scopeJoinReactionCounterOfType` method of `Reactable` model trait
- ([#102]) Renamed virtual column `reactions_total_count` to `reaction_total_count` in `scopeJoinReactionTotal` method of `Reactable` model trait
- ([#102]) Renamed virtual column `reactions_total_weight` to `reaction_total_weight` in `scopeJoinReactionTotal` method of `Reactable` model trait

### Removed

- ([#86]) Laravel 5.6 support obsolete
- ([#79]) Removed `isReactedToWithType` method from `Reacter` model contract
- ([#79]) Removed `isNotReactedToWithType` method from `Reacter` model contract
- ([#79]) Removed `isReactedByWithType` method from `Reactant` model contract
- ([#79]) Removed `isNotReactedByWithType` method `Reactant` model contract
- ([#89]) Removed `scopeWhereReactedByWithType` method from `Reactable` model trait

## [7.2.1] - 2019-07-11

### Fixed

- ([#77]) Fixed Null Objects iterable return type inconsistency

## [7.2.0] - 2019-07-01

### Added

- ([#72]) Default migrations loading configuration

## [7.1.0] - 2019-06-23

### Added

- ([#68]) Database connection configuration

## [7.0.1] - 2019-06-22

### Changed

- ([#70]) `isReactedTo` & `isNotReactedTo` methods names of `Reacter` facade were changed to `hasReactedTo` & `hasNotReactedTo`

## [7.0.0] - 2019-06-22

### Added

- ([#54]) `Reacter` & `Reactant` facades
- `viaLoveReacter` method to `Reacterable` trait & contract
- `viaLoveReactant` method to `Reactable` trait & contract

### Removed

- ([#67]) `Cog\Laravel\Love\Facades\Love` global facade

## [6.2.1] - 2019-06-14

### Fixed

- ([#58]) Fix `--model` option of `love:setup-reacterable` & `love:setup-reactable` Artisan commands

## [6.2.0] - 2019-06-14

### Added

- ([#56]) `love:setup-reacterable` & `love:setup-reactable` Artisan commands were added

## [6.1.0] - 2019-05-07

### Added

- ([#51]) `love:reaction-type-add` Artisan command was added

## [6.0.1] - 2019-03-05

### Removed

- ([#47]) Removed duplicating indexes for foreign keys

## [6.0.0] - 2019-02-25

Package API was refactored from a scratch.
Code has a lot of breaking changes and cannot be updated easily.

Follow [upgrade instructions](UPGRADING.md#from-v5-to-v6) to migrate database to new structure.

### Added

- Laravel 5.8 support
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
- `ReactionCounter` updates on background using queue
- `ReactionTotal` updates on background using queue

### Removed

- Laravel 5.5 support obsolete
- PHP < 7.1.3 support obsolete
- `LikeableWasLiked` event was removed
- `LikeableWasDisliked` event was removed
- `LikeableWasUnliked` event was removed
- `LikeableWasUndisliked` event was removed

## [5.2.0] - 2018-09-09

### Added

- ([#21]) Laravel 5.7 support

## [5.1.1] - 2018-02-16

### Fixed

- ([#11]) Added missing migrations auto-loading

## [5.1.0] - 2018-02-08

### Added

- ([#9]) Laravel 5.6 support

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

[Unreleased]: https://github.com/cybercog/laravel-love/compare/8.9.0...master
[8.9.0]: https://github.com/cybercog/laravel-love/compare/8.8.1...8.9.0
[8.8.1]: https://github.com/cybercog/laravel-love/compare/8.8.0...8.8.1
[8.8.0]: https://github.com/cybercog/laravel-love/compare/8.7.1...8.8.0
[8.7.1]: https://github.com/cybercog/laravel-love/compare/8.7.0...8.7.1
[8.7.0]: https://github.com/cybercog/laravel-love/compare/8.6.1...8.7.0
[8.6.1]: https://github.com/cybercog/laravel-love/compare/8.6.0...8.6.1
[8.6.0]: https://github.com/cybercog/laravel-love/compare/8.5.0...8.6.0
[8.5.0]: https://github.com/cybercog/laravel-love/compare/8.4.0...8.5.0
[8.4.0]: https://github.com/cybercog/laravel-love/compare/8.3.1...8.4.0
[8.3.1]: https://github.com/cybercog/laravel-love/compare/8.3.0...8.3.1
[8.3.0]: https://github.com/cybercog/laravel-love/compare/8.2.0...8.3.0
[8.2.0]: https://github.com/cybercog/laravel-love/compare/8.1.2...8.2.0
[8.1.2]: https://github.com/cybercog/laravel-love/compare/8.1.1...8.1.2
[8.1.1]: https://github.com/cybercog/laravel-love/compare/8.1.0...8.1.1
[8.1.0]: https://github.com/cybercog/laravel-love/compare/8.0.0...8.1.0
[8.0.0]: https://github.com/cybercog/laravel-love/compare/7.2.1...8.0.0
[7.2.1]: https://github.com/cybercog/laravel-love/compare/7.2.0...7.2.1
[7.2.0]: https://github.com/cybercog/laravel-love/compare/7.1.0...7.2.0
[7.1.0]: https://github.com/cybercog/laravel-love/compare/7.0.1...7.1.0
[7.0.1]: https://github.com/cybercog/laravel-love/compare/7.0.0...7.0.1
[7.0.0]: https://github.com/cybercog/laravel-love/compare/6.2.1...7.0.0
[6.2.1]: https://github.com/cybercog/laravel-love/compare/6.2.0...6.2.1
[6.2.0]: https://github.com/cybercog/laravel-love/compare/6.1.0...6.2.0
[6.1.0]: https://github.com/cybercog/laravel-love/compare/6.0.1...6.1.0
[6.0.1]: https://github.com/cybercog/laravel-love/compare/6.0.0...6.0.1
[6.0.0]: https://github.com/cybercog/laravel-love/compare/5.2.0...6.0.0
[5.3.0]: https://github.com/cybercog/laravel-love/compare/5.2.1...5.3.0
[5.2.1]: https://github.com/cybercog/laravel-love/compare/5.2.0...5.2.1
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

[#206]: https://github.com/cybercog/laravel-love/pull/206
[#197]: https://github.com/cybercog/laravel-love/pull/197
[#196]: https://github.com/cybercog/laravel-love/pull/196
[#187]: https://github.com/cybercog/laravel-love/pull/187
[#186]: https://github.com/cybercog/laravel-love/pull/186
[#185]: https://github.com/cybercog/laravel-love/pull/185
[#178]: https://github.com/cybercog/laravel-love/pull/178
[#177]: https://github.com/cybercog/laravel-love/pull/177
[#176]: https://github.com/cybercog/laravel-love/pull/176
[#165]: https://github.com/cybercog/laravel-love/pull/165
[#161]: https://github.com/cybercog/laravel-love/pull/161
[#158]: https://github.com/cybercog/laravel-love/pull/158
[#151]: https://github.com/cybercog/laravel-love/pull/151
[#148]: https://github.com/cybercog/laravel-love/pull/148
[#147]: https://github.com/cybercog/laravel-love/pull/147
[#146]: https://github.com/cybercog/laravel-love/pull/146
[#127]: https://github.com/cybercog/laravel-love/pull/127
[#121]: https://github.com/cybercog/laravel-love/pull/121
[#118]: https://github.com/cybercog/laravel-love/pull/118
[#114]: https://github.com/cybercog/laravel-love/pull/114
[#113]: https://github.com/cybercog/laravel-love/pull/113
[#111]: https://github.com/cybercog/laravel-love/pull/111
[#110]: https://github.com/cybercog/laravel-love/pull/110
[#102]: https://github.com/cybercog/laravel-love/pull/102
[#100]: https://github.com/cybercog/laravel-love/pull/100
[#99]: https://github.com/cybercog/laravel-love/pull/99
[#97]: https://github.com/cybercog/laravel-love/pull/97
[#96]: https://github.com/cybercog/laravel-love/pull/96
[#91]: https://github.com/cybercog/laravel-love/pull/91
[#90]: https://github.com/cybercog/laravel-love/pull/90
[#89]: https://github.com/cybercog/laravel-love/pull/89
[#88]: https://github.com/cybercog/laravel-love/pull/88
[#87]: https://github.com/cybercog/laravel-love/pull/87
[#86]: https://github.com/cybercog/laravel-love/pull/86
[#83]: https://github.com/cybercog/laravel-love/pull/83
[#79]: https://github.com/cybercog/laravel-love/pull/79
[#77]: https://github.com/cybercog/laravel-love/pull/77
[#72]: https://github.com/cybercog/laravel-love/pull/72
[#70]: https://github.com/cybercog/laravel-love/pull/70
[#68]: https://github.com/cybercog/laravel-love/pull/68
[#67]: https://github.com/cybercog/laravel-love/pull/67
[#58]: https://github.com/cybercog/laravel-love/pull/58
[#56]: https://github.com/cybercog/laravel-love/pull/56
[#54]: https://github.com/cybercog/laravel-love/pull/54
[#51]: https://github.com/cybercog/laravel-love/pull/51
[#47]: https://github.com/cybercog/laravel-love/pull/47
[#21]: https://github.com/cybercog/laravel-love/pull/21
[#11]: https://github.com/cybercog/laravel-love/pull/11
[#9]: https://github.com/cybercog/laravel-love/pull/9
