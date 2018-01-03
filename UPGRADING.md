# Upgrade Guide

- [From v3 to v4](#from-v3-to-v4)

## From v3 to v4

Because there are many breaking changes an upgrade is not that easy. There are many edge cases this guide does not cover.
We accept PRs to improve this guide.

Exceptions namespace were moved to the contracts namespace, were renamed
and extends `\RuntimeException` instead of `\Exception` now.

- Find all `Cog\Likeable\Exceptions\LikeTypeInvalidException` and replace with `Cog\Contracts\Love\Like\Exceptions\InvalidLikeType`
- Find all `Cog\Likeable\Exceptions\ModelInvalidException` and replace with `Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable`
- Find all `Cog\Likeable\Exceptions\LikerNotDefinedException` and replace with `Cog\Contracts\Love\Liker\Exceptions\InvalidLiker`

Find and replace: 

- Find all `Cog\Likeable\Contracts\Like` and replace with `Cog\Contracts\Love\Like\Models\Like`
- Find all `Cog\Likeable\Contracts\Likeable` and replace with `Cog\Contracts\Love\Likeable\Models\Likeable`
- Find all `Cog\Likeable\Contracts\LikeableService` and replace with `Cog\Contracts\Love\Likeable\Services\LikeableService`
- Find all `Cog\Likeable\Traits\Likeable` and replace with `Cog\Laravel\Love\Likeable\Models\Traits\Likeable`
- Find all `Cog\Likeable` and replace with `Cog\Laravel\Likeable`

If you have used `Cog\Likeable\Observers\ModelObserver` observer you need to use new one `Cog\Laravel\Love\Likeable\Observers\LikeableObserver`

These database changes should be performed:

- Rename `like` table to `likes`
- Rename `like_counter` table to `like_counters`
- Update name of migration file in `migrations` table from `2016_09_02_153301_create_like_table` to `2016_09_02_153301_create_likes_table`
- Update name of migration file in `migrations` table from `2016_09_02_163301_create_like_counter_table` to `2016_09_02_163301_create_like_counters_table`

To make all changes in MySQL you could run these 5 commands one by one:

```mysql
ALTER TABLE `like` RENAME TO `love_likes`;

ALTER TABLE `like_counter` RENAME TO `love_like_counters`;

UPDATE `migrations`
   SET `migration` = '2016_09_02_153301_create_love_likes_table'
 WHERE `migration` = '2016_09_02_153301_create_like_table'
 LIMIT 1;
 
UPDATE `migrations`
   SET `migration` = '2016_09_02_163301_create_love_like_counters_table'
 WHERE `migration` = '2016_09_02_163301_create_like_counter_table'
 LIMIT 1;
```

Migration files:

- Delete `database/migrations/2016_09_02_153301_create_like_table.php` & `database/migrations/2016_09_02_163301_create_like_counter_table.php` migration files (since v4 service provider automatically loading migration files or republish it if custom changes are required to be done).
