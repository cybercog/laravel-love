# Upgrade Guide

- [From v3 to v4](#from-v3-to-v4)

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
