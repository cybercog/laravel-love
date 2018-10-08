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
- [Usage](#usage)
  - [Prepare Liker Model](#prepare-liker-model)
  - [Prepare Likeable Model](#prepare-likeable-model)
  - [Available Methods](#available-methods)
  - [Scopes](#scopes)
  - [Events](#events)
  - [Console Commands](#console-commands)
- [Extending](#extending)
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
- `Reactant` can has many types of reactions.
- `Reacter` can add many reactions to one `Reactant` or they could be mutually exclusive.
- Sort `Reactable` models by reactions total count.
- Sort `Reactable` models by reactions total weight.
- Events for `created` & `deleted` reactions.
- Has Artisan command `love:recount {model?} {type?}` to re-fetch reactions stats.
- Designed to work with Laravel Eloquent models.
- Using contracts to keep high customization capabilities.
- Using traits to get functionality out of the box.
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
$ php artisan vendor:publish --provider="Cog\Laravel\Love\Providers\LoveServiceProvider" --tag=migrations
```

## Usage

### Prepare Models

To start using package you need to have:

1. At least one `Reacterable` model, which will act as `Reacter` and will react to the content. 
2. At least one `Reactable` model, which will act as `Reactant` and will receive reactions.

#### Prepare Reacterable Model

Declare `Cog\Contracts\Love\Reacterable\Models\Reacterable` contract
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

#### Prepare Reactable Model

Declare `Cog\Contracts\Love\Reactable\Models\Reactable` contract
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

### Create Required Related Models

#### Initialize Reacters

To let `User` react to the content create `Reacter` model for it. 

```php
$user->reacter()->create();
``` 

*Creation of the `Reacter` need to be done only once for each `Reacterable` model and usually done automatically on model creation.*

To start reacting to the content you can always get `Reacter` model and use all its methods.

```php
$reacter = $user->reacter()->first();
```

#### Initialize Reactants

To let content receive creations create `Reactant` model for it. 

```php
$article->reactant()->create();
``` 

*Creation of the `Reactant` need to be done only once for each `Reactable` model and usually done automatically on model creation.*

To start reacting to the content you can always get `Reactant` model and use all its methods.

```php
$reactant = $article->reactant()->first();
```

#### Initialize Reaction Types

> TODO: How to create reaction types

### Available Methods

#### Reaction Type Methods

##### Get reaction type by name

```php
$reactionType = ReactionType::fromName('Like');
```

##### Get reaction type name

```php
$reactionType->getName(); // 'Like'
```

##### Get reaction type weight

```php
$reactionType->getWeight(); // 3
```

#### Reacter Methods

##### React to the content

```php
$reacter->reactTo($reactant, $reactionType);
```

##### Remove Reaction from the content

```php
$reacter->unreactTo($reactant, $reactionType);
```

##### Check if Reacter reacted to Reactant

> TODO: *Checks in eager loaded relations `reactions` first.*

Determine if Reacter reacted to Reactant with any type of Reaction.

```php
$isReacted = $reacter->isReactedTo($reactant);

$isNotReacted = $reacter->isNotReactedTo($reactant);
```

Determine if Reacter reacted to Reactant with exact type of Reaction.

```php
$reactionType = ReactionType::fromName('Like');

$isReacted = $reacter
    ->isReactedWithTypeTo($reactant, $reactionType);

$isNotReacted = $reacter
    ->isNotReactedWithTypeTo($reactant, $reactionType);
```

#### Reactant Methods

##### Get Reaction Counters of the Reactant

```php
$reactant->getReactionCounters();
```

##### Get Reactions which Reactant received

```php
$reactant->getReactions();
```

#### Stats

##### Get difference between likes and dislikes

> Too complicated... need to simplify

```php
// $article->getReactant()->getSummary()->getTotalWeight();
// $article->getReactant()->getSummary()->totalWeight();

// TODO: I want to write it this way.
// $article->reactantSummary()->getTotalWeight();
// or
// $article->getTotalReactionsWeight();
```

### Scopes

##### Find all Articles reacted by User

> TODO: Design an API

```php
Article::whereReactedBy($user->reacter)
    ->with([
        'reactionCounters', // Eager load (optional)
        'reactionSummary',
    ])
    ->get();
```

##### Sort Reactable models by Reactions Total Count

```php
$sortedArticles = Article::orderByReactionsCount()->get();
$sortedArticles = Article::orderByReactionsCount('asc')->get();
```

*Uses `desc` as default order direction.*

##### Sort Reactable models by Reactions Total Weight

```php
$sortedArticles = Article::orderByReactionsWeight()->get();
$sortedArticles = Article::orderByReactionsWeight('asc')->get();
```

*Uses `desc` as default order direction.*

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

## Extending

You can override core classes of package with your own implementations:

- `Cog\Laravel\Love\Like\Models\Like`
- `Cog\Laravel\Love\LikeCounter\Models\LikeCounter`
- `Cog\Laravel\Love\Reactable\Services\ReactableService`

*Note: Don't forget that all custom models must implement original models interfaces.*

To make it you should use container [binding interfaces to implementations](https://laravel.com/docs/master/container#binding-interfaces-to-implementations) in your application service providers.

##### Use model class own implementation

```php
$this->app->bind(
    \Cog\Contracts\Love\Like\Models\Like::class,
    \App\Models\CustomLike::class
);
```

##### Use service class own implementation

```php
$this->app->singleton(
    \Cog\Contracts\Love\Reactable\Services\ReactableService::class,
    \App\Services\CustomService::class
);
```

After that your `CustomLike` and `CustomService` classes will be instantiable with helper method `app()`.

```php
$model = app(\Cog\Contracts\Love\Like\Models\Like::class);
$service = app(\Cog\Contracts\Love\Reactable\Services\ReactableService::class);
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

- `Laravel Love` package is open-sourced software licensed under the [MIT license](LICENSE) by Anton Komarev.
- `Devil` image licensed under [Creative Commons 3.0](https://creativecommons.org/licenses/by/3.0/us/) by YuguDesign.

## About CyberCog

[CyberCog](http://www.cybercog.ru) is a Social Unity of enthusiasts. Research best solutions in product & software development is our passion.

- [Follow us on Twitter](https://twitter.com/cybercog)
- [Read our articles on Medium](https://medium.com/cybercog)

<a href="http://cybercog.ru"><img src="https://cloud.githubusercontent.com/assets/1849174/18418932/e9edb390-7860-11e6-8a43-aa3fad524664.png" alt="CyberCog"></a>
