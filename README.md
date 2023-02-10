# Niklan.net

<img src="./web/themes/custom/mechanical/logo.svg" alt="Niklan.net" width="128" align="right">

[![PHPCS](https://github.com/Niklan/niklan.net/actions/workflows/phpcs.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/phpcs.yml) [![PHPStan](https://github.com/Niklan/niklan.net/actions/workflows/phpstan.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/phpstan.yml) [![PHPUnit](https://github.com/Niklan/niklan.net/actions/workflows/phpunit.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/phpunit.yml)

This is a repository with a source code of <https://niklan.net> website.

> **WARNING!**\
> It's not intended to be used other than for educational purposes. Backward compatibility and support is not provided. If you want something to ask me about that code, you can [contact me](https://niklan.net/contact) directly.

## System requirements

- PHP 8.1+
- [Drupal's system requirements](https://www.drupal.org/docs/system-requirements) for the rest not mentioned above.

## Installation

- Clone repository
  ```shell
  git clone https://github.com/Niklan/niklan.net.git
  ```
- Install PHP dependencies
  ```shell
  composer install
  ```
- Create custom `settings.local.php` and adjust settings if needed
  ```shell
  cp web/sites/default/settings.local.example.php web/sites/default/settings.local.php
  ```
- Create custom `.env.php` file and adjust settings if needed
  ```shell
  cp web/.env.example.php web/.env.php
  ```
- Open your local domain, e.g.: https://niklan.localhost
- Follow installation instructions.
- Make a cup of ☕ or 🍵 and wait until the installation is finished.

## Quality tools

The project uses PHPCS and PHPStan for checkin quality of the code and code-style.

### PHPCS

PHPCS is configured in [phpcs.xml](phpcs.xml). It uses Drupal PHPCS rules with additional one from [chi-teck/drupal-coder-extension](https://github.com/Chi-teck/drupal-coder-extension) package for modern PHP syntax improvements.

**Run PHPCS:**

```shell
composer phpcs
```

### PHPStan

PHPStan is configured in [phpstan.neon](phpstan.neon). It uses [mglaman/phpstan-drupal](https://github.com/mglaman/phpstan-drupal) on top of default ones. Currently, it is on level 1, because level 2 requires changes in Drupal core directly.

**Run PHPStan:**

```shell
composer phpstan
```

## Testing

The project is uses PHPUnit for testing its codebase.

### PHPUnit

PHPUnit is extended with [weitzman/drupal-test-traits](https://gitlab.com/weitzman/drupal-test-traits) for some «existing site» testing.

**Run PHPUnit:**

```shell
composer phpunit
```

**Run PHPUnit from Docker 4 Drupal containers:** (see [d4d-php.sh](scripts/d4d-php.sh) for explanation)

```shell
composer d4d-phpunit
```

## Contribution

This is a personal project, it's not expects any contributions to it. You can send Pull Request with typo fixes and maybe other improvements, but don't expect them to be merged. You better do not do that.

## Feedback

If you want to ask me something about this project, or it's code-base, feel free to [contact me](https://niklan.net/contact).
