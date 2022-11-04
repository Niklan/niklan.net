# Niklan.net

<img src="./web/themes/custom/mechanical/logo.svg" alt="Niklan.net" width="128" align="right">

[![PHPCS](https://github.com/Niklan/niklan.net/actions/workflows/phpcs.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/phpcs.yml) [![PHPStan](https://github.com/Niklan/niklan.net/actions/workflows/phpstan.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/phpstan.yml) [![PHPUnit](https://github.com/Niklan/niklan.net/actions/workflows/phpunit.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/phpunit.yml)

This repository contains source code of my personal blog <https://niklan.net>.

**WARNING!** It's not intended to be used other than for educational purposes. Backward compatibility is not provided. There is no issues, if you want something to ask me about that code, you can contact me directly.

## Installation

- Clone repository `git clone https://github.com/Niklan/niklan.net.git`.
- Run `composer install`.
- Create custom `settings.local.php` by `cp web/sites/default/settings.local.example.php web/sites/default/settings.local.php`. Adjust settings if needed.
- Run `yarn install && yarn run compile`.
- Open your local domain, e.g.: https://niklan.localhost
- Follow installation instructions.
- Make a cup of ☕ or 🍵 and wait until the installation is finished.
