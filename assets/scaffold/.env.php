<?php

/**
 * @file
 * This is '.env' file used by the project.
 *
 * Copy it to '.env.php' to override.
 *
 * This is PHP file for several reasons:
 * - vlucas/phpdotenv adds 3ms overhead on each request. This is actually huge,
 *   considering that Drupal can throw response in 1-3ms as well from cache,
 *   this is insane. Even if we consider that Drupal respond by 30ms, this is
 *   10% overhead just for static data. Using it directly is a bad idea in that
 *   case, it requires some caching mechanism, but it can be very tricky to
 *   do without some external CLI command (like Drush) to rebuild this cached
 *   values, because they must be available before Drupal start connection to
 *   Database and must be updated as soon as .env is changed.
 * - It sets environment variables via $_ENV instead of putenv()/getenv(),
 *   because these functions are thread unsafe.
 *   See: https://github.com/vlucas/phpdotenv/issues/76
 *
 * Be careful. This file allows to override existing ENV variables from system.
 *
 * Do not put sensitive credentials into scaffold (assets/scaffold) version of
 * this file.
 */

/**
 * Database connection settings.
 */
$_ENV['DATABASE_NAME'] = 'drupal';
$_ENV['DATABASE_USERNAME'] = 'drupal';
$_ENV['DATABASE_PASSWORD'] = 'drupal';
$_ENV['DATABASE_HOST'] = 'mariadb';
$_ENV['DATABASE_PORT'] = '3306';

/**
 * SMTP settings.
 */
$_ENV['SMTP_HOST'] = 'mailhog';
$_ENV['SMTP_PORT'] = '1025';
$_ENV['SMTP_USERNAME'] = '';
$_ENV['SMTP_PASSWORD'] = '';

/**
 * Drupal core specific settings.
 */
// See $settings['hash_salt'] for more information.
$_ENV['HASH_SALT'] = 'put_your_salt_here';

/**
 * Project specific settings.
 */
$_ENV['SHOW_DEVELOPMENT_WARNING'] = TRUE;
