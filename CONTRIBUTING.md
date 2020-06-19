# Contributing to Laravel Love

Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project you agree to abide by its terms.

## Workflow

- Fork the project.
- Make your bug fix or feature addition.
- Add tests for it. This is important so we don't break it in a future version unintentionally.
- Send a pull request. Bonus points for topic branches.

Please make sure that you have [set up your user name and email address](http://git-scm.com/book/en/v2/Getting-Started-First-Time-Git-Setup) for use with Git.

Pull requests for bug fixes must be based on the current stable branch.

We are trying to keep backwards compatibility breaks in Laravel Love to an absolute minimum. Please take this into account when proposing changes.

Due to time constraints, we are not always able to respond as quickly as we would like. Please do not take delays personal and feel free to remind us if you feel that we forgot to respond.

## Coding Guidelines

This package attempts to comply with [PSR-4] autoloader, [PSR-1] basic coding standard and [PSR-12] coding style guide.
If you notice compliance oversights, please send a patch via pull request.

## PHPUnit tests

The phpunit script can be used to invoke the PHPUnit test runner:

```shell script
$ vendor/bin/phpunit
```

## Reporting issues

- [General problems]

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[PSR-12]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md
[General problems]: https://github.com/cybercog/laravel-love/issues
