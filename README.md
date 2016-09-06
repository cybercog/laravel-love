# Laravel Likeable

[![Build Status](https://travis-ci.org/cybercog/laravel-likeable.svg)](https://travis-ci.org/cybercog/laravel-likeable)
[![Latest Stable Version](https://poser.pugx.org/cybercog/laravel-likeable/version)](https://packagist.org/packages/cybercog/laravel-likeable)
[![License](https://poser.pugx.org/cybercog/laravel-likeable/license.svg)](https://github.com/cybercog/laravel-likeable/blob/master/LICENSE)

Trait for Laravel Eloquent models to allow easy implementation of a `like` & `dislike` features.

*Note: Likes and dislikes for one model by one user are mutually exclusive.*

## Installation

First, pull in the package through Composer.

```shell
composer require cybercog/laravel-likeable "^1.0"
```

And then include the service provider within `app/config/app.php`.

```php
'providers' => [
	Cog\Likeable\Providers\LikeableServiceProvider::class,
],
```

At last you need to publish and run the migration.

```shell
php artisan vendor:publish --provider="Cog\Likeable\Providers\LikeableServiceProvider" --tag=migrations
php artisan migrate
```

## Usage

### Prepare model

Use `HasLikes` contract in model which will get likes behavior and implement it or just use `HasLikes` trait. 

```php
use Cog\Likeable\Contracts\HasLikes as HasLikesContract;
use Cog\Likeable\Traits\HasLikes;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements HasLikesContract {
	use HasLikes;
}
```

### Available functions

#### Likes

Like model

```php
$article->like(); // current user
$article->like($user->id);
```

Remove like mark from model

```php
$article->unlike(); // current user
$article->unlike($user->id);
```

Toggle like mark of model

```php
$article->likeToggle(); // current user
$article->likeToggle($user->id);
```

Get model likes count

```php
$article->likesCount;
```

Get model likes counter

```php
$article->likeCounter;
```

Get iterable `Illuminate\Database\Eloquent\Collection` of existing model likes

```php
$article->likes;
```

Boolean check if user liked model

```php
$article->liked; // check if currently logged in user liked the article
$article->liked(); // check if currently logged in user liked the article
$article->liked($user->id);
```

Find all articles liked by user

```php
Article::whereLikedBy($user->id)
	->with('likeCounter') // Allow eager load (optional)
	->get();
```

Delete all likes for model

```php
$article->removeLikes();
```

#### Dislikes

Dislike model

```php
$article->dislike(); // current user
$article->dislike($user->id);
```

Remove dislike mark from model

```php
$article->undislike(); // current user
$article->undislike($user->id);
```

Toggle dislike mark of model

```php
$article->dislikeToggle(); // current user
$article->dislikeToggle($user->id);
```

Get model dislikes count

```php
$article->dislikesCount;
```

Get model dislikes counter

```php
$article->dislikeCounter;
```

Get iterable `Illuminate\Database\Eloquent\Collection` of existing model dislikes

```php
$article->dislikes;
```

Boolean check if user disliked model

```php
$article->disliked; // check if currently logged in user liked the article
$article->disliked(); // check if currently logged in user liked the article
$article->disliked($user->id);
```

Find all articles disliked by user

```php
Article::whereDislikedBy($user->id)
	->with('likeCounter') // Allow eager load (optional)
	->get();
```

Delete all dislikes for model

```php
$article->removeDislikes();
```

#### Likes and Dislikes

Get difference between likes and dislikes

```php
$article->likesDiffDislikesCount;
```

Get iterable `Illuminate\Database\Eloquent\Collection` of existing model likes and dislikes

```php
$article->likesAndDislikes;
```

### Events

On each like added `\Cog\Likeable\Events\ModelWasLiked` event is fired.

On each like removed `\Cog\Likeable\Events\ModelWasUnliked` event is fired.

On each dislike added `\Cog\Likeable\Events\ModelWasDisliked` event is fired.

On each dislike removed `\Cog\Likeable\Events\ModelWasUndisliked` event is fired.

### Console commands

Recount likes and dislikes of all model types

```shell
likeable:recount
```

Recount likes and dislikes of concrete model type (using morph map alias)

```shell
likeable:recount --model="article"
```

Recount likes and dislikes of concrete model type (using fully qualified class name)

```shell
likeable:recount --model="App\Models\Article"
```

Recount only likes of all model types

```shell
likeable:recount --type="like"
```

Recount only likes of concrete model type (using morph map alias)

```shell
likeable:recount --model="article" --type="like"
```

Recount only likes of concrete model type (using fully qualified class name)

```shell
likeable:recount --model="App\Models\Article" --type="like"
```

Recount only dislikes of all model types

```shell
likeable:recount --type="dislike"
```

Recount only dislikes of concrete model type (using morph map alias)

```shell
likeable:recount --model="article" --type="dislike"
```

Recount only dislikes of concrete model type (using fully qualified class name)

```shell
likeable:recount --model="App\Models\Article" --type="dislike"
```
