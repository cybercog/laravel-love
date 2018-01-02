# Changelog

All notable changes to `laravel-likeable` will be documented in this file.

## [4.0.0] - 2017-12-29

### Changed

- Database table `like` renamed to `likes`
- Database table `like_counter` renamed to `like_counters`
- Contracts extracted to namespace `Cog\Contracts\Likeable`
- `ModelObserver` class renamed to `LikeableObserver`
- `LikeType::LIKE` & `LikeType::DISLIKE` constant values are uppercase now `LIKE` & `DISLIKE` respectively
- Exceptions extends `\RuntimeException` instead of `\Exception`
- Exception `LikeTypeInvalidException` moved from `Cog\Contracts\Likeable\Exceptions` to `Cog\Contracts\Likeable\Like\Exceptions`
- Exception `LikeTypeInvalidException` renamed to `InvalidLikeType`
- Exception `LikerNotDefinedException` moved from `Cog\Contracts\Likeable\Exceptions` to `Cog\Contracts\Likeable\Liker\Exceptions`
- Exception `LikerNotDefinedException` renamed to `LikerNotDefined`
- Exception `ModelInvalidException` moved from `Cog\Contracts\Likeable\Exceptions` to `Cog\Contracts\Likeable\Likeable\Exceptions`
- Exception `ModelInvalidException` renamed to `InvalidLikeable`
- Console command `LikeableRecount` moved from `Cog\Likeable\Console` to `Cog\Laravel\Likeable\Console\Commands`
- Console command `LikeableRecount` renamed to `Recount`

### Removed

- Deleted deprecated `Cog\Likeable\Contracts\HasLikes` contract
- Deleted deprecated `Cog\Likeable\Traits\HasLikes` trait
- `scopeWhereLikedBy` method from `Cog\Likeable\Contracts\LikeableService` contract
- `scopeWhereLikedBy` method from `Cog\Likeable\Services\LikeableService` class
- `scopeOrderByLikesCount` method from `Cog\Likeable\Contracts\LikeableService` contract
- `scopeOrderByLikesCount` method from `Cog\Likeable\Services\LikeableService` class
- `getLikesCountAttribute` method from `Cog\Likeable\Contracts\Likeable` contract
- `getDislikesCountAttribute` method from `Cog\Likeable\Contracts\Likeable` contract
- `getLikedAttribute` method from `Cog\Likeable\Contracts\Likeable` contract
- `getDislikedAttribute` method from `Cog\Likeable\Contracts\Likeable` contract
- `getLikesDiffDislikesCountAttribute` method from `Cog\Likeable\Contracts\Likeable` contract
- `scopeWhereLikedBy` method from `Cog\Likeable\Contracts\Likeable` contract
- `scopeWhereDislikedBy` method from `Cog\Likeable\Contracts\Likeable` contract
- `scopeOrderByLikesCount` method from `Cog\Likeable\Contracts\Likeable` contract
- `scopeOrderByDislikesCount` method from `Cog\Likeable\Contracts\Likeable` contract

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

- `orderByDislikesCount` scope added to `HasLikes` trait.
- `scopeOrderByLikesCount` method to `LikeableService`.

### Fixed

- `orderByLikesCount` count only likes now.

## [2.2.3] - 2017-04-20

### Fixed

`orderByLikesCount` work in MySQL databases.

## [2.2.2] - 2017-04-09

### Fixed

- `orderByLikesCount` returns models without likes too.

## [2.2.1] - 2017-04-09

### Fixed

- `orderByLikesCount` database query fixed.

## [2.2.0] - 2017-04-09

### Added

- `Article::orderByLikesCount('asc')` scope for the model. Uses `desc` as default order direction.

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

[4.0.0]: https://github.com/cybercog/laravel-likeable/compare/3.1.0...4.0.0
[3.1.0]: https://github.com/cybercog/laravel-likeable/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/cybercog/laravel-likeable/compare/2.2.5...3.0.0
[2.2.5]: https://github.com/cybercog/laravel-likeable/compare/2.2.4...2.2.5
[2.2.4]: https://github.com/cybercog/laravel-likeable/compare/2.2.3...2.2.4
[2.2.3]: https://github.com/cybercog/laravel-likeable/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/cybercog/laravel-likeable/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/cybercog/laravel-likeable/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/cybercog/laravel-likeable/compare/2.1.0...2.2.0
[2.1.0]: https://github.com/cybercog/laravel-likeable/compare/2.0.1...2.1.0
[2.0.1]: https://github.com/cybercog/laravel-likeable/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/cybercog/laravel-likeable/compare/1.1.2...2.0.0
[1.1.2]: https://github.com/cybercog/laravel-likeable/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/cybercog/laravel-likeable/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/cybercog/laravel-likeable/compare/1.0.0...1.1.0
