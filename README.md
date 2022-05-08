<p align="center">
  <img src="./web/themes/custom/mechanical/logo.svg" alt="Niklan.net" width="64">
</p>

# Niklan.net

This repository contains source code of my personal blog <https://niklan.net>.

**WARNING!** It's not intended to be used other than for educational purposes. Backward compatibility is not provided. There is no issues, if you want something to ask me about that code, you can contact me directly.

## Installation

- Clone repository `git clone https://github.com/Niklan/niklan.net.git`.
- Run `composer install`.
- Run `yarn install && yarn run compile`.
- Open your local domain, e.g.: https://niklan.localhost
- Follow installation instructions.
- Make a cup of ☕ or 🍵 and wait until the installation is finished.

## Settings overrides

To protect sensitive information stored in configs, these settings should be adjusted:

```php
// Emails are send using SMTP module, provide proper values.
// Values below is for Docker4Drupal mailhog integration and testing.
$config['smtp.settings']['smtp_on'] = TRUE;
$config['smtp.settings']['smtp_host'] = 'mailhog';
$config['smtp.settings']['smtp_port'] = 1025;
$config['smtp.settings']['smtp_protocol'] = 'standard';
$config['smtp.settings']['smtp_username'] = '';
$config['smtp.settings']['smtp_password'] = '';
$config['smtp.settings']['smtp_from'] = 'example@example.com';
$config['smtp.settings']['smtp_fromname'] = 'John Doe';
```
