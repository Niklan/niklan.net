<?php

// phpcs:ignoreFile

/**
 * @file
 * The 'live' environment settings overrides.
 */

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 *
 * This variable will be set to a random value by the installer. All one-time
 * login links will be invalidated if the value is changed. Note that if your
 * site is deployed on a cluster of web servers, you must ensure that this
 * variable has the same value on each server.
 *
 * For enhanced security, you may set this variable to the contents of a file
 * outside your document root; you should also ensure that this file is not
 * stored with backups of your database.
 *
 * Example:
 * @code
 *   $settings['hash_salt'] = file_get_contents('/home/example/salt.txt');
 * @endcode
 */
$settings['hash_salt'] = NULL;

/**
 * Database connection settings.
 */
$databases['default']['default'] = [
  'database' => 'drupal',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => 'mariadb',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];

/**
 * Symfony Mailer module configuration.
 *
 * SMTP is a default transport.
 */
$config['symfony_mailer.mailer_transport.smtp']['configuration']['user'] = NULL;
$config['symfony_mailer.mailer_transport.smtp']['configuration']['pass'] = NULL;
$config['symfony_mailer.mailer_transport.smtp']['configuration']['host'] = NULL;
$config['symfony_mailer.mailer_transport.smtp']['configuration']['port'] = NULL;