# Upgrade Guide

- [From v7 to v8](#from-v7-to-v8)
- [From v6 to v7](#from-v6-to-v7)
- [From v5 to v6](#from-v5-to-v6)
- [From v4 to v5](#from-v4-to-v5)
- [From v3 to v4](#from-v3-to-v4)

## From v7 to v8

- Find all `isReactedTo` method usages and replace it with `hasReactedTo`
- Find all `isReactedToWithType` method usages and replace it with `hasReactedTo`
- Find all `isNotReactedTo` method usages and replace it with `hasNotReactedTo`
- Find all `isNotReactedToWithType` method usages and replace it with `hasNotReactedTo`
- Find all `isReactedByWithType` method usages and replace it with `isReactedBy`
- Find all `isNotReactedByWithType` method usages and replace it with `isNotReactedBy`

## From v6 to v7

You can skip upgrading for this version if you haven't used `Love` facade and using packaged traits in `Reacterable` & `Reactant` models. 

Release v7 has new facade approach described in ([#54](https://github.com/cybercog/laravel-love/issues/54)).
Global `Cog\Laravel\Love\Facades\Love` facade was removed in favor to new facades:
- `Cog\Laravel\Love\Reacter\Facades\Reacter`
- `Cog\Laravel\Love\Reactant\Facades\Reactant`

If you are implemented `Cog\Contracts\Love\Reactable\Models\Reactable` & `Cog\Contracts\Love\Reacterable\Models\Reacterable` contracts by yourself without using packaged traits you need to implement 2 new methods: 
- `Reactable` model should have `viaLoveReactant` method
- `Reacterable` model should have `viaLoveReacter` method

## From v5 to v6

Release v6 is a total package refactoring with a lot of breaking changes.
Most of the upgrade requirements couldn't be done automatically because of completely different API. 

### Prepare models

- Replace all `Cog\Contracts\Love\Likeable\Models\Likeable` with `Cog\Contracts\Love\Reactable\Models\Reactable`
- Replace all `Cog\Laravel\Love\Likeable\Models\Traits\Likeable` with `Cog\Laravel\Love\Reactable\Models\Traits\Reactable`
- Replace all `Cog\Contracts\Love\Liker\Models\Liker` with `Cog\Contracts\Love\Reacterable\Models\Reacterable`
- Replace all `Cog\Laravel\Love\Liker\Models\Traits\Liker` with `Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable`

### Prepare database tables

- Add `$table->unsignedBigInteger('love_reacter_id');` column to each table which models can react on content.
- Add `$table->unsignedBigInteger('love_reactant_id');` column to each table which models can be reacted.

### Reactable model methods

- Find all `whereLikedBy` method and replace it with `whereReactedWithTypeBy`
- Find all `whereDislikedBy` method and replace it with `whereReactedWithTypeBy`
- Find all `like` method and replace it with `reactTo`
- Find all `dislike` method and replace it with `reactTo`
- Find all `unlike` method and replace it with `unreactTo`
- Find all `undislike` method and replace it with `unreactTo`
- Find all `orderByLikesCount` method and replace it with `joinReactionCounterOfType` and common `orderBy`
- Find all `orderByDislikesCount` method and replace it with `joinReactionCounterOfType` and common `orderBy`

### Automatic migration process

Run only after all preparations are done.

**VERY IMPORTANT: Create backup of your production database!**

```sh
php artisan love:upgrade-v5-to-v6
```

## From v4 to v5

### Likeable model methods

- Find all `like` method and replace it with `likeBy`
- Find all `dislike` method and replace it with `dislikeBy`
- Find all `unlike` method and replace it with `unlikeBy`
- Find all `undislike` method and replace it with `undislikeBy`
- Find all `likeToggle` method and replace it with `toggleLikeBy`
- Find all `dislikeToggle` method and replace it with `toggleDislikeBy`
- Find all `liked` method and replace it with `isLikedBy`
- Find all `disliked` method and replace it with `isDislikedBy`

## From v3 to v4

Because there are many breaking changes an upgrade is not that easy. There are many edge cases this guide does not cover.
We accept PRs to improve this guide.

### Console Commands

- If you have used `Cog\Likeable\Console\LikeableRecountCommand` console command you should use new name `Cog\Laravel\Love\Console\Commands\Recount`
- Note that command signature was changed from `likeable:recount {model?} {type?}` to `love:recount {model?} {type?}`

### Events

- Find all `Cog\Likeable\Events\ModelWasDisliked` and replace with `Cog\Laravel\Love\Likeable\Events\LikeableWasDisliked`
- Find all `Cog\Likeable\Events\ModelWasLiked` and replace with `Cog\Laravel\Love\Likeable\Events\LikeableWasLiked`
- Find all `Cog\Likeable\Events\ModelWasUndisliked` and replace with `Cog\Laravel\Love\Likeable\Events\LikeableWasUndisliked`
- Find all `Cog\Likeable\Events\ModelWasUnliked` and replace with `Cog\Laravel\Love\Likeable\Events\LikeableWasUnliked`
- In all listeners which intercepts Likeable's events described above, replace `$event->model` to `$event->likeable`

### Exceptions

Exceptions namespace were moved to the contracts namespace, were renamed
and extends `\RuntimeException` instead of `\Exception` now.

- Find all `Cog\Likeable\Exceptions\LikeTypeInvalidException` and replace with `Cog\Contracts\Love\Like\Exceptions\InvalidLikeType`
- Find all `Cog\Likeable\Exceptions\ModelInvalidException` and replace with `Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable`
- Find all `Cog\Likeable\Exceptions\LikerNotDefinedException` and replace with `Cog\Contracts\Love\Liker\Exceptions\InvalidLiker`

### Models

- Find all `Cog\Likeable\Contracts\Like` and replace with `Cog\Contracts\Love\Like\Models\Like`
- Find all `Cog\Likeable\Contracts\Likeable` and replace with `Cog\Contracts\Love\Likeable\Models\Likeable`
- Find all `Cog\Likeable\Traits\Likeable` and replace with `Cog\Laravel\Love\Likeable\Models\Traits\Likeable`

### Models Observers

If you have used `Cog\Likeable\Observers\ModelObserver` observer you should use new name `Cog\Laravel\Love\Likeable\Observers\LikeableObserver`.

### Services

- Find all `Cog\Likeable\Contracts\LikeableService` and replace with `Cog\Contracts\Love\Likeable\Services\LikeableService`

### Database

#### Perform Migrations Manually

To make all changes in MySQL you could run these commands one by one.

**Don't forget to make full database backup before making an upgrade!** 

Rename `like` table to `love_likes`:

```mysql
ALTER TABLE `like` RENAME TO `love_likes`;
```

Update name of migration file in `migrations` table from `2016_09_02_153301_create_like_table` to `2016_09_02_153301_create_love_likes_table`:

```mysql
UPDATE `migrations`
   SET `migration` = '2016_09_02_153301_create_love_likes_table'
 WHERE `migration` = '2016_09_02_153301_create_like_table'
 LIMIT 1;
```

Rename `like_counter` table to `love_like_counters`:

```mysql
ALTER TABLE `like_counter` RENAME TO `love_like_counters`;
```

Update name of migration file in `migrations` table from `2016_09_02_163301_create_like_counter_table` to `2016_09_02_163301_create_like_counters_table`:

```mysql
UPDATE `migrations`
   SET `migration` = '2016_09_02_163301_create_love_like_counters_table'
 WHERE `migration` = '2016_09_02_163301_create_like_counter_table'
 LIMIT 1;
```

Add nullable `updated_at` column to `love_likes` table:

```mysql
ALTER TABLE `love_likes`
 ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;
```

Sync `updated_at` column of `love_likes` table with `created_at` column (optional):

```mysql
UPDATE `love_likes`
   SET `updated_at` = `created_at`
 WHERE `updated_at` IS NULL
   AND id > 0;
```

Add nullable `created_at` & `updated_at` columns to `love_like_counters` table:

```mysql
ALTER TABLE `love_like_counters` 
 ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT NULL AFTER `count`,
 ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;
```

#### Migration Files Changes

Since v4 service provider automatically loading migration files, so you could delete
`database/migrations/2016_09_02_153301_create_like_table.php` and
`database/migrations/2016_09_02_163301_create_like_counter_table.php` migration files.

If you need to have them locally you could republish them and re-apply your local changes to keep them up to date.
