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

Laravel Love is emotional part of the application. It let people express how they feel about the content.
Make any model reactable in a minutes!

There are many different implementations in modern applications:

- Github Reactions
- Facebook Reactions
- YouTube Likes
- Slack Reactions
- Medium Claps

This package developed in mind that it should cover all the possible use cases and be viable in enterprise applications.

It is a successor of the very simple abandoned package:
[Laravel Likeable](https://github.com/cybercog/laravel-likeable).

## Contents

- [Features](#features)
- [System Design](#system-design)
- [Glossary](#glossary)
- [Requirements](#requirements)
- [Installation](#installation)
- [Integration](#integration)
  - [Prepare Reacterable Models](#prepare-reacterable-models)
  - [Prepare Reactable Models](#prepare-reactable-models)
- [Usage](#usage)
  - [Reaction Types](#reaction-types)
  - [Reacterables](#reacterables)
  - [Reacters](#reacters)
  - [Reactables](#reactables)
  - [Reactants](#reactants)
  - [Reactant Reaction Counters](#reactant-reaction-counters)
  - [Reactant Reaction Totals](#reactant-reaction-totals)
  - [Reactable Scopes](#reactable-scopes)
  - [Facades](#facades)
  - [Eager Loading](#eager-loading)
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
- Reactant can has many types of reactions.
- Reacter can add many reactions to one reactant.
- Reaction counters with detailed aggregated data for each reactant.
- Reaction totals with total aggregated data for each reactant.
- Can work with any database `id` column types.
- Sort reactable models by reactions total count.
- Sort reactable models by reactions total weight.
- Events for added & removed reactions.
- Has Artisan command `love:recount {model?} {type?}` to re-fetch reactions stats.
- Designed to work with Laravel Eloquent models.
- Using contracts to keep high customization capabilities.
- Using traits to get functionality out of the box.
- Using database foreign keys.
- Using Null Object design pattern.
- Strict typed.
- Following PHP Standard Recommendations:
  - [PSR-1 (Basic Coding Standard)](http://www.php-fig.org/psr/psr-1/).
  - [PSR-2 (Coding Style Guide)](http://www.php-fig.org/psr/psr-2/).
  - [PSR-4 (Autoloading Standard)](http://www.php-fig.org/psr/psr-4/).
- Covered with unit tests.

## System Design

![190102-cog-laravel-love-uml](https://user-images.githubusercontent.com/1849174/50601995-0fed4700-0ec7-11e9-856b-2856f4c58f67.png)

## Glossary

- `Reaction` — the response that reveals Reacter's feelings or attitude.
- `ReactionType` — type of the emotional response (Like, Dislike, Love, Hate, etc).
- `Reacterable` — User, Person, Organization or any other model which can act as Reacter.
- `Reacter` — one who reacts.
- `Reactable` — Article, Comment, User or any other model which can act as Reactant.
- `Reactant` — subject which could receive Reactions.
- `ReactionCounter` — aggregated statistical values of ReactionTypes related to Reactant.
- `ReactionTotal` — aggregated statistical values of total reactions count & their weight related to Reactant.

## Requirements

Laravel Love has a few requirements you should be aware of before installing:

- PHP 7.1.3+
- Composer
- Laravel Framework 5.6+

## Installation

Pull in the package through Composer.

```sh
$ composer require cybercog/laravel-love
```

Run database migrations.

```sh
$ php artisan migrate
```

If you want to make changes in migrations, publish them to your application first.

```sh
$ php artisan vendor:publish --tag=love-migrations
```

After installing Love, add reaction types using the `love:reaction-type-add` Artisan command.
Or add default `Like` & `Dislike` types using `--default` option.

```sh
$ php artisan love:reaction-type-add --default
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

After that create & run migrations which will add unsigned big integer column `love_reacter_id`
to each database table where reacterable models are stored.

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('love_reacter_id');
    });
}
```

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

After that create & run migrations which will add unsigned big integer column `love_reactant_id`
to each database table where reactable models are stored.

```php
public function up(): void
{
    Schema::table('articles', function (Blueprint $table) {
        $table->unsignedBigInteger('love_reactant_id');
    });
}
```

## Usage

### Reaction Types

`ReactionType` model describes how `Reacter` reacted to `Reactant`.
By default there are 2 types of reactions `Like` and `Dislike`.
Each `Like` adds `+1` to reactant's total weight while `Dislike` type subtract `-1` from it.

#### Instantiate reaction type from name

```php
$reactionType = ReactionType::fromName('Like');
```

#### Get type name

```php
$typeName = $reactionType->getName(); // 'Like'
```

#### Get type weight

```php
$typeWeight = $reactionType->getWeight(); // 1
```

#### Determine types equality

```php
$likeType = ReactionType::fromName('Like'); 
$dislikeType = ReactionType::fromName('Dislike'); 

$isEqual = $likeType->isEqualTo($likeType); // true
$isEqual = $likeType->isEqualTo($dislikeType); // false

$isNotEqual = $likeType->isNotEqualTo($likeType); // false
$isNotEqual = $likeType->isNotEqualTo($dislikeType); // true
```

### Reacterables

#### Register reacterable as reacter

To let `User` react to the content it need to be registered as `Reacter`.

By default it will be done automatically on successful `Reacterable` creation,
but if this behavior was changed you still can do it manually. 

```php
$user->registerAsLoveReacter();
```

*Creation of the `Reacter` could be done only once for each `Reacterable` model.*

If you will try to register `Reacterable` as `Reacter` one more time then
`Cog\Contracts\Love\Reacterable\Exceptions\AlreadyRegisteredAsLoveReacter` exception will be thrown.

> If you want to skip auto-creation of related `Reacter` model just add boolean method
> `shouldRegisterAsLoveReacterOnCreate` to `Reacterable` model which will return `false`.

#### Verify reacter registration

If you want to verify if `Reacterable` is registered as `Reacter` or not you can use boolean methods.

```php
$isRegistered = $user->isRegisteredAsLoveReacter(); // true

$isNotRegistered = $user->isNotRegisteredAsLoveReacter(); // false
```

#### Get reacter model

Only `Reacter` model can react to content. Get `Reacter` model from your `Reacterable` model. 

```php
$reacter = $user->getLoveReacter();
```

> If `Reacterable` model is not registered as `Reacter` you will receive `NullReacter` model instead
> (NullObject design pattern). All it's methods will be callable, but will throw exceptions or return `false`.

### Reacters

#### Get reacterable

```php
$reacterable = $reacter->getReacterable();
```

#### React to reactant

```php
$reacter->reactTo($reactant, $reactionType);
```

#### Remove reaction from reactant

```php
$reacter->unreactTo($reactant, $reactionType);
```
#### Check if reacter reacted to reactant

Determine if `Reacter` reacted to `Reactant` with any type of reaction.

```php
$isReacted = $reacter->isReactedTo($reactant);

$isNotReacted = $reacter->isNotReactedTo($reactant);
```

Determine if `Reacter` reacted to `Reactant` with exact type of reaction.

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

### Reactables

#### Register reactable as reactant

To let `Article` to receive reactions from users it need to be registered as `Reactant`.

By default it will be done automatically on successful `Reactable` creation,
but if this behavior was changed you still can do it manually. 

```php
$user->registerAsLoveReactant();
```

*Creation of the `Reactant` could be done only once for each `Reactable` model.*

If you will try to register `Reactable` as `Reactant` one more time then
`Cog\Contracts\Love\Reactable\Exceptions\AlreadyRegisteredAsLoveReactant` exception will be thrown.

> If you want to skip auto-creation of related `Reactant` model just add boolean method
> `shouldRegisterAsLoveReactantOnCreate` to `Reactable` model which will return `false`.

#### Verify reactant registration

If you want to verify if `Reactable` is registered as `Reactant` or not you can use boolean methods.

```php
$isRegistered = $user->isRegisteredAsLoveReactant(); // true

$isNotRegistered = $user->isNotRegisteredAsLoveReactant(); // false
```

#### Get reactant model

Only `Reacter` model can react to content. Get `Reacter` model from your `Reactable` model. 

```php
$reactant = $user->getLoveReactant();
```

> If `Reactable` model is not registered as `Reactant` you will receive `NullReactant` model instead
> (NullObject design pattern). All it's methods will be callable, but will throw exceptions or return `false`.

### Reactants

#### Get reactable model

```php
$reactable = $reactant->getReactable();
```

#### Check if reactant reacted by reacter

Determine if `Reacter` reacted to `Reactant` with any type of reaction.

```php
$isReacted = $reactant->isReactedBy($reacter);

$isNotReacted = $reactant->isNotReactedBy($reacter);
```

Determine if `Reacter` reacted to `Reactant` with exact type of reaction.

```php
$reactionType = ReactionType::fromName('Like');

$isReacted = $reactant
    ->isReactedByWithType($reacter, $reactionType);

$isNotReacted = $reactant
    ->isNotReactedByWithType($reacter, $reactionType);
```

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

> If each `Like` has weight `+1` and `Dislike` has weight `-1`
> then 3 likes and 5 dislikes will produce `-2` total weight.  

### Reactable Scopes

#### Find all reactables reacted by user

```php
$reacter = $user->getLoveReacter();

Article::query()
    ->whereReactedBy($reacter)
    ->get();
```

#### Find all reactables reacted by user with exact type of reaction

```php
$reacter = $user->getLoveReacter();
$reactionType = ReactionType::fromName('Like');

$articles = Article::query()
    ->whereReactedByWithType($reacter, $reactionType)
    ->get();
```

#### Add reaction counter aggregate of exact reaction type to reactables

```php
$reactionType = ReactionType::fromName('Like'); 

$articles = Article::query()
    ->joinReactionCounterOfType($reactionType)
    ->get();
```

Each Reactable model will contain `reactions_count` & `reactions_weight` virtual attributes.

After adding counter aggregate models could be ordered by this value.

```php
$articles = Article::query()
    ->joinReactionCounterOfType($reactionType)
    ->orderBy('reactions_count', 'desc')
    ->get();
```

#### Add reaction total aggregate to reactables

```php
$articles = Article::query()
    ->joinReactionTotal()
    ->get();
```

Each Reactable model will contain `reactions_total_count` & `reactions_total_weight` virtual attributes.

After adding total aggregate models could be ordered by this values.

Order by `reactions_total_count`:

```php
$articles = Article::query()
    ->joinReactionTotal()
    ->orderBy('reactions_total_count', 'desc')
    ->get();
```

Order by `reactions_total_weight`:

```php
$articles = Article::query()
    ->joinReactionTotal()
    ->orderBy('reactions_total_weight', 'desc')
    ->get();
```

### Facades

Laravel Love ships with `Love` facade and allows to execute actions as `Reacterable` model
instead of acting as `Reacter` and affect on `Reactable` models instead of `Reactant`. 

> Note: Love facade is experimental feature which will be refactored in next releases.
> Try to avoid it's usage if possible.

#### Determine if reaction of type

```php
$isOfType = Love::isReactionOfTypeName($reaction, 'Like');

$isNotOfType = Love::isReactionNotOfTypeName($reaction, 'Like');
```

Same functionality without facade:

```php
$reactionType = ReactionType::fromName('Like');

$isOfType = $reaction->isOfType($reactionType);

$isNotOfType = $reaction->isNotOfType($reactionType);
```

#### Determine if reacterable reacted to reactable

```php
$isReacted = Love::isReacterableReactedTo($user, $article);

$isNotReacted = Love::isReacterableNotReactedTo($user, $article);
```

Same functionality without facade:

```php
$reactant = $article->getLoveReactant();

$isReacted = $reacterable
    ->getLoveReacter()
    ->isReactedTo($reactant);

$isNotReacted = $reacterable
    ->getLoveReacter()
    ->isNotReactedTo($reactant);
```

#### Determine if reacterable reacted to reactable with reaction type name

```php
$isReacted = Love::isReacterableReactedToWithTypeName($user, $article, 'Like');

$isReacted = Love::isReacterableNotReactedToWithTypeName($user, $article, 'Like');
```

Same functionality without facade:

```php
$reactant = $article->getLoveReactant();
$reactionType = ReactionType::fromName('Like');

$isReacted = $reacterable
    ->getLoveReacter()
    ->isReactedToWithType($reactant, $reactionType);

$isNotReacted = $reacterable
    ->getLoveReacter()
    ->isNotReactedToWithType($reactant, $reactionType);
```

#### Determine if reactable reacted by reacterable

```php
$isReacted = Love::isReactableReactedBy($article, $user);

$isReacted = Love::isReactableNotReactedBy($article, $user);
```

Same functionality without facade:

```php
$reacter = $user->getLoveReacter();

$isReacted = $reactable
    ->getLoveReactant()
    ->isReactedBy($reacter);

$isNotReacted = $reactable
    ->getLoveReactant()
    ->isNotReactedBy($reacter);
```

#### Determine if reactable reacted by reacterable with reaction type name

```php
$isReacted = Love::isReactableReactedByWithTypeName($article, $user, 'Like');

$isReacted = Love::isReactableNotReactedByWithTypeName($article, $user, 'Like');
```

Same functionality without facade:

```php
$reacter = $user->getLoveReacter();
$reactionType = ReactionType::fromName('Like');

$isReacted = $reactable
    ->getLoveReactant()
    ->isReactedByWithType($reacter, $reactionType);

$isNotReacted = $reactable
    ->getLoveReactant()
    ->isNotReactedByWithType($reacter, $reactionType);
```

#### Get reactable count of reactions for type name

```php
$likesCount = Love::getReactableReactionsCountForTypeName($article, 'Like');
```

Same functionality without facade:

```php
$reactionType = ReactionType::fromName('Like');

$likesCount = $reactable
    ->getLoveReactant()
    ->getReactionCounterOfType($reactionType)
    ->getCount();
```

#### Get reactable weight of reactions for type name

```php
$likesWeight = Love::getReactableReactionsWeightForTypeName($article, 'Like');
```

Same functionality without facade:

```php
$reactionType = ReactionType::fromName('Like');

$likesWeight = $reactable
    ->getLoveReactant()
    ->getReactionCounterOfType($reactionType)
    ->getWeight();
```

#### Get reactable reactions total count

```php
$reactionsTotalCount = Love::getReactableReactionsTotalCount($article);
```

Same functionality without facade:

```php
$reactionsTotalCount = $reactable
    ->getLoveReactant()
    ->getReactionTotal()
    ->getCount();
```

#### Get reactable reactions total weight

```php
$reactionsTotalWeight = Love::getReactableReactionsTotalWeight($article);
```

Same functionality without facade:

```php
$reactionsTotalWeight = $reactable
    ->getLoveReactant()
    ->getReactionTotal()
    ->getWeight();
```

### Eager Loading

When accessing Eloquent relationships as properties, the relationship data is "lazy loaded".
This means the relationship data is not actually loaded until you first access the property.
However, Eloquent can "eager load" relationships at the time you query the parent model.
Eager loading alleviates the N + 1 query problem.
More details read in [official Laravel documentation](https://laravel.com/docs/master/eloquent-relationships#eager-loading).

List of the most common eager loaded relations:

- `loveReactant.reactions.type`
- `loveReactant.reactions.reacter.reacterable`
- `loveReactant.reactionCounters`
- `loveReactant.reactionTotal`

```php
$articles = Article::query()
    ->with([
        'loveReactant.reactions.reacter.reacterable',
        'loveReactant.reactions.type',
        'loveReactant.reactionCounters',
        'loveReactant.reactionTotal',
    ])
    ->get();
```

### Events

On each added reaction `Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded` event is fired.

On each removed reaction `Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved` event is fired.

### Console Commands

#### Recount likes and dislikes of all model types

```sh
$ love:recount
```

#### Recount likes and dislikes of concrete model type (using morph map alias)

```sh
$ love:recount --model="article"
```

#### Recount likes and dislikes of concrete model type (using fully qualified class name)

```sh
$ love:recount --model="App\Models\Article"
```

#### Recount only likes of all model types

```sh
$ love:recount --type="Like"
```

#### Recount only likes of concrete model type (using morph map alias)

```sh
$ love:recount --model="article" --type="Like"
```

#### Recount only likes of concrete model type (using fully qualified class name)

```sh
$ love:recount --model="App\Models\Article" --type="Like"
```

#### Recount only dislikes of all model types

```sh
$ love:recount --type="Dislike"
```

#### Recount only dislikes of concrete model type (using morph map alias)

```sh
$ love:recount --model="article" --type="Dislike"
```

#### Recount only dislikes of concrete model type (using fully qualified class name)

```sh
$ love:recount --model="App\Models\Article" --type="Dislike"
```

#### Add reaction type

```sh
$ love:reaction-type-add
```

> Note: Type names transformed to StudlyCase. Name `very-good` will be converted to `VeryGood`.

#### Add reaction type without interaction

```sh
$ love:reaction-type-add name=Hate weight=-4
```

#### Add default reaction types

Creates `Like` with weight `1` and `Dislike` with weight `-1`.

```sh
$ love:reaction-type-add --default
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
