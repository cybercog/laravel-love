# Laravel Love

![cog-laravel-love](https://user-images.githubusercontent.com/1849174/34500991-094a66da-f01e-11e7-9a6c-0480f1564338.png)

<p align="center">
<a href="https://github.com/cybercog/laravel-love/releases"><img src="https://img.shields.io/github/release/cybercog/laravel-love.svg?style=flat-square" alt="Releases"></a>
<a href="https://travis-ci.org/cybercog/laravel-love"><img src="https://img.shields.io/travis/cybercog/laravel-love/master.svg?style=flat-square" alt="Build Status"></a>
<a href="https://styleci.io/repos/116058336"><img src="https://styleci.io/repos/116058336/shield" alt="StyleCI"></a>
<a href="https://scrutinizer-ci.com/g/cybercog/laravel-love/?branch=master"><img src="https://img.shields.io/scrutinizer/g/cybercog/laravel-love.svg?style=flat-square" alt="Code Quality"></a>
<a href="https://github.com/cybercog/laravel-love/blob/master/LICENSE"><img src="https://img.shields.io/github/license/cybercog/laravel-love.svg?style=flat-square" alt="License"></a>
</p>

## Introduction

Laravel Love simplify management of Eloquent model's multi typed reactions. Make any model reactable in a minutes!

This package is a fork of the more simple but abandoned package: [Laravel Likeable](https://github.com/cybercog/laravel-likeable).

## Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Integration](#integration)
  - [Prepare Reacterable Models](#prepare-reacterable-models)
  - [Prepare Reactable Models](#prepare-reactable-models)
- [Usage](#usage)
  - [Reaction Types](#reaction-types)
  - [Reacters](#reacters)
  - [Reactants](#reactants)
  - [Reactant Reaction Counters](#reactant-reaction-counters)
  - [Reactant Reaction Totals](#reactant-reaction-totals)
  - [Scopes](#scopes)
  - [Events](#events)
  - [Console Commands](#console-commands)
- [Changelog](#changelog)
- [Upgrading](#upgrading)
- [Contributing](#contributing)
- [Testing](#testing)
- [Security](#security)
- [Contributors](#contributors)
- [Alternatives](#alternatives)
- [License](#license)
- [About CyberCog](#about-cybercog)

## Features

- Fully customizable types of reactions.
- Any model can react to models and receive reactions at the same time.
- `Reactant` can has many types of reactions.
- `Reacter` can add many reactions to one `Reactant` or they could be mutually exclusive.
- Reaction counters with detailed aggregated data for each reactant.
- Reaction totals with total aggregated data for each reactant.
- Can work with any database `id` column types.
- Sort `Reactable` models by reactions total count.
- Sort `Reactable` models by reactions total weight.
- Events for `created` & `deleted` reactions.
- Has Artisan command `love:recount {model?} {type?}` to re-fetch reactions stats.
- Designed to work with Laravel Eloquent models.
- Using contracts to keep high customization capabilities.
- Using traits to get functionality out of the box.
- Strict typed.
- Using Null Object design pattern.
- Following PHP Standard Recommendations:
  - [PSR-1 (Basic Coding Standard)](http://www.php-fig.org/psr/psr-1/).
  - [PSR-2 (Coding Style Guide)](http://www.php-fig.org/psr/psr-2/).
  - [PSR-4 (Autoloading Standard)](http://www.php-fig.org/psr/psr-4/).
- Covered with unit tests.

## Requirements

Laravel Love has a few requirements you should be aware of before installing:

- PHP 7.1.3+
- Composer
- Laravel Framework 5.6+

## Installation

First, pull in the package through Composer.

```sh
$ composer require cybercog/laravel-love
```

#### Perform Database Migration

At last you need to publish and run database migrations.

```sh
$ php artisan migrate
```

If you want to make changes in migrations, publish them to your application first.

```sh
$ php artisan vendor:publish --tag=love-migrations
```

## Integration

To start using package you need to have:

1. At least one `Reacterable` model, which will act as `Reacter` and will react to the content. 
2. At least one `Reactable` model, which will act as `Reactant` and will receive reactions.

### Prepare Reacterable Models

Each model which can act as `Reacter` and will react to content must implement `Reacterable` contract.

Declare that model implements `Cog\Contracts\Love\Reacterable\Models\Reacterable` contract
and use `Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable` trait. 

```php
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements ReacterableContract
{
    use Reacterable;
}
```

> TODO: Write that BIGINT `love_reacter_id` database column must be added

### Prepare Reactable Models

Each model which can act as `Reactant` and will receive reactions must implement `Reactable` contract.

Declare that model implements `Cog\Contracts\Love\Reactable\Models\Reactable` contract
and use `Cog\Laravel\Love\Reactable\Models\Traits\Reactable` trait. 

```php
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements ReactableContract
{
    use Reactable;
}
```

> TODO: Write that BIGINT `love_reactant_id` database column must be added

## Usage

### Reaction Types

`ReactionType` model describes how `Reacter` reacted to `Reactant`.
By default there are 2 types of reactions `Like` and `Dislike`.

`Like` type adds `+1` weight to reactant's importance while `Dislike` type subtract `-1` from it's weight.

##### Instantiate Reaction Type From Name

```php
$reactionType = ReactionType::fromName('Like');
```

##### Get Type Name 

```php
$typeName = $reactionType->getName(); // 'Like'
```

##### Get Type Weight

```php
$typeWeight = $reactionType->getWeight(); // 3
```

##### Determine Types Equality

```php
$likeType = ReactionType::fromName('Like'); 
$dislikeType = ReactionType::fromName('Dislike'); 

$likeType->isEqualTo($likeType); // true
$likeType->isEqualTo($dislikeType); // false

$likeType->isNotEqualTo($likeType); // false
$likeType->isNotEqualTo($dislikeType); // true
```

### Reacters

#### Register as reacter

To let `User` react to the content it need to be registered as `Reacter`.

By default it will be done automatically on successful `Reacterable` creation,
but if this behavior was changed you still can do it manually. 

```php
$user->registerAsLoveReacter();
```

*Creation of the `Reacter` could be done only once for each `Reacterable` model.*

If you will try to register `Reacterable` as `Reacter` one more time then
`Cog\Contracts\Love\Reacterable\Exceptions\AlreadyRegisteredAsLoveReacter` exception will be thrown.

> TODO: How to skip auto creation of Reacter

#### Verify reacter registration

If you want to verify if `Reacterable` is registered as `Reacter` or not you can use boolean methods.

```php
$isRegistered = $user->isRegisteredAsLoveReacter(); // true

$isNotRegistered = $user->isNotRegisteredAsLoveReacter(); // false
```

#### Get reacter model

Only `Reacter` model can react to content. Get `Reacter` model from your `Reacterable` model. 

```php
$reacter = $user->getReacter();
```

> If `Reacterable` model is not registered as `Reacter` you will receive `NullReacter` model instead (also known as NullObject design pattern).
> All it's methods will be callable, but will throw exceptions or return `false`.

#### React to reactant

```php
$reacter->reactTo($reactant, $reactionType);
```

#### Remove reaction from reactant

```php
$reacter->unreactTo($reactant, $reactionType);
```
#### Check if reacter reacted to reactant

Determine if `Reacter` reacted to `Reactant` with any type of Reaction.

```php
$isReacted = $reacter->isReactedTo($reactant);

$isNotReacted = $reacter->isNotReactedTo($reactant);
```

Determine if Reacter reacted to Reactant with exact type of Reaction.

```php
$reactionType = ReactionType::fromName('Like');

$isReacted = $reacter
    ->isReactedToWithType($reactant, $reactionType);

$isNotReacted = $reacter
    ->isNotReactedToWithType($reactant, $reactionType);
```

#### Get reactions which reacter has made

```php
$reactions = $reacter->getReactions();
```

> TODO: Need to add pagination

### Reactants

#### Register as reactant

To let `Article` to receive reactions from users it need to be registered as `Reactant`.

By default it will be done automatically on successful `Reactable` creation,
but if this behavior was changed you still can do it manually. 

```php
$user->registerAsLoveReactant();
```

*Creation of the `Reactant` could be done only once for each `Reactable` model.*

If you will try to register `Reactable` as `Reactant` one more time then
`Cog\Contracts\Love\Reactable\Exceptions\AlreadyRegisteredAsLoveReactant` exception will be thrown.

> TODO: How to skip auto creation of Reactant

#### Verify reactant registration

If you want to verify if `Reactable` is registered as `Reactant` or not you can use boolean methods.

```php
$isRegistered = $user->isRegisteredAsLoveReactant(); // true

$isNotRegistered = $user->isNotRegisteredAsLoveReactant(); // false
```

#### Get reactant model

Only `Reacter` model can react to content. Get `Reacter` model from your `Reactable` model. 

```php
$reactant = $user->getReactant();
```

> If `Reactable` model is not registered as `Reactant` you will receive `NullReactant` model instead (also known as NullObject design pattern).
> All it's methods will be callable, but will throw exceptions or return `false`.

#### Get reactions which reactant received

```php
$reactions = $reactant->getReactions();
```

> TODO: Need to add pagination

### Reactant Reaction Counters

Each `Reactant` has many counters (one for each reaction type) with aggregated data.

#### Get reaction counters of reactant

```php
$reactionCounters = $reactant->getReactionCounters();
```

Or get only counter of exact type.

```php
$reactionType = ReactionType::fromName('Like');

$reactionCounter = $reactant->getReactionCounterOfType($reactionType);
```

#### Get reactions count

When you need to determine count of reactions of this type you can get count.

```php
$totalWeight = $reactionCounter->getCount();
```

#### Get reactions weight

When you need to determine weight which all reactions of this type gives you can get weight.

```php
$totalWeight = $reactionCounter->getWeight();
```

### Reactant Reaction Totals

Each `Reactant` has one total with aggregated data. Total is sum of counters of all reaction types.

#### Get reaction total of reactant

```php
$reactionTotal = $reactant->getReactionTotal();
```

#### Get reactions total count

When you need to determine total reactions count you can get count.

```php
$totalWeight = $reactionTotal->getCount();
```

#### Get reactions total weight

When you need to determine total weight of reactions you can get weight.

```php
$totalWeight = $reactionTotal->getWeight();
```

> If each `Like` has weight `+1` and `Dislike` has weight `-1` then 3 likes and 5 dislikes will return `-2` total weight.  

### Scopes

#### Find all articles reacted by user

```php
$reacter = $user->getReacter();

Article::whereReactedBy($reacter)->get();
```

#### Find all articles reacted by user with exact type of reaction

```php
$reacter = $user->getReacter();
$reactionType = ReactionType::fromName('Like');

Article::whereReactedByWithTypeOf($reacter, $reactionType)->get();
```

#### Add reaction counter aggregate of exact reaction type to reactables

```php
$reactionType = ReactionType::fromName('Like'); 

$articles = Article::joinReactionCounterWithType($reactionType)->get();
```

Each Reactable model will contain extra column: `reactions_count`.

You can order Reactables by `reactions_count`:

```php
$articles = Article::withReactionCounterTypeOf($reactionType)
    ->orderBy('reactions_count', 'desc')->get();
```

#### Add reaction total aggregate to reactables

```php
$articles = Article::withReactionTotal()->get();
```

Each Reactable model will contain extra columns: `reactions_total_count` & `reactions_total_weight`.

You can order Reactables by `reactions_total_count`:

```php
$articles = Article::withReactionTotal()
    ->orderBy('reactions_total_count', 'desc')->get();
```

You can order Reactables by `reactions_total_weight`:

```php
$articles = Article::withReactionTotal()
    ->orderBy('reactions_total_weight', 'desc')->get();
```

### Events

On each added reaction `\Cog\Laravel\Love\Reaction\Events\ReactionWasCreated` event is fired.

On each removed reaction `\Cog\Laravel\Love\Reaction\Events\ReactionWasDeleted` event is fired.

### Console Commands

##### Recount likes and dislikes of all model types

```sh
$ love:recount
```

##### Recount likes and dislikes of concrete model type (using morph map alias)

```sh
$ love:recount --model="article"
```

##### Recount likes and dislikes of concrete model type (using fully qualified class name)

```sh
$ love:recount --model="App\Models\Article"
```

##### Recount only likes of all model types

```sh
$ love:recount --type="Like"
```

##### Recount only likes of concrete model type (using morph map alias)

```sh
$ love:recount --model="article" --type="Like"
```

##### Recount only likes of concrete model type (using fully qualified class name)

```sh
$ love:recount --model="App\Models\Article" --type="Like"
```

##### Recount only dislikes of all model types

```sh
$ love:recount --type="Dislike"
```

##### Recount only dislikes of concrete model type (using morph map alias)

```sh
$ love:recount --model="article" --type="Dislike"
```

##### Recount only dislikes of concrete model type (using fully qualified class name)

```sh
$ love:recount --model="App\Models\Article" --type="Dislike"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Upgrading

Please see [UPGRADING](UPGRADING.md) for detailed upgrade instructions.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Testing

Run the tests with:

```sh
$ vendor/bin/phpunit
```

## Security

If you discover any security related issues, please email open@cybercog.su instead of using the issue tracker.

## Contributors

| <a href="https://github.com/antonkomarev">![@antonkomarev](https://avatars.githubusercontent.com/u/1849174?s=110)<br />Anton Komarev</a> | <a href="https://github.com/squigg">![@squigg](https://avatars.githubusercontent.com/u/4279310?s=110)<br />Squigg</a> | <a href="https://github.com/acidjazz">![@acidjazz](https://avatars.githubusercontent.com/u/967369?s=110)<br />Kevin Olson</a> | <a href="https://github.com/raniesantos">![@raniesantos](https://avatars.githubusercontent.com/u/8528269?s=110)<br />Ranie Santos</a> |  
| :---: | :---: | :---: | :---: |

[Laravel Love contributors list](../../contributors)

## Alternatives

- [cybercog/laravel-likeable](https://github.com/cybercog/laravel-likeable)
- [rtconner/laravel-likeable](https://github.com/rtconner/laravel-likeable)
- [faustbrian/laravel-likeable](https://github.com/faustbrian/Laravel-Likeable)
- [sukohi/evaluation](https://github.com/SUKOHI/Evaluation)
- [zvermafia/lavoter](https://github.com/zvermafia/lavoter)
- [francescomalatesta/laravel-reactions](https://github.com/francescomalatesta/laravel-reactions)
- [muratbsts/laravel-reactable](https://github.com/muratbsts/laravel-reactable)
- [hkp22/laravel-reactions](https://github.com/hkp22/laravel-reactions)

*Feel free to add more alternatives as Pull Request.*

## License

- `Laravel Love` package is open-sourced software licensed under the [MIT license](LICENSE) by [Anton Komarev](https://github.com/antonkomarev).
- `Devil` image licensed under [Creative Commons 3.0](https://creativecommons.org/licenses/by/3.0/us/) by YuguDesign.

## About CyberCog

[CyberCog](http://www.cybercog.ru) is a Social Unity of enthusiasts. Research best solutions in product & software development is our passion.

- [Follow us on Twitter](https://twitter.com/cybercog)
- [Read our articles on Medium](https://medium.com/cybercog)

<a href="http://cybercog.ru"><img src="https://cloud.githubusercontent.com/assets/1849174/18418932/e9edb390-7860-11e6-8a43-aa3fad524664.png" alt="CyberCog"></a>
