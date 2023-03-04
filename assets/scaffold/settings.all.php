<?php

/**
 * @file
 * This file contains additional settings for all environments.
 */

/**
 * Database connection settings.
 */
$databases['default']['default'] = [
  'database' => $_ENV['DATABASE_NAME'],
  'username' => $_ENV['DATABASE_USERNAME'],
  'password' => $_ENV['DATABASE_PASSWORD'],
  'prefix' => '',
  'host' => $_ENV['DATABASE_HOST'],
  'port' => $_ENV['DATABASE_HOST'],
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];

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
$settings['hash_salt'] = $_ENV['HASH_SALT'];

/**
 * Location of the site configuration files.
 *
 * The $settings['config_sync_directory'] specifies the location of file system
 * directory used for syncing configuration data. On install, the directory is
 * created. This is used for configuration imports.
 *
 * The default location for this directory is inside a randomly-named
 * directory in the public files path. The setting below allows you to set
 * its location.
 */
$settings['config_sync_directory'] = '../config/sync';

/**
 * Skip file system permissions hardening.
 *
 * The system module will periodically check the permissions of your site's
 * site directory to ensure that it is not writable by the website user. For
 * sites that are managed with a version control system, this can cause problems
 * when files in that directory such as settings.php are updated, because the
 * user pulling in the changes won't have permissions to modify files in the
 * directory.
 */
$settings['skip_permissions_hardening'] = TRUE;

/**
 * Increase cache bin sizes.
 */
$settings['database_cache_max_rows']['default'] = 10_000;

/**
 * Symfony Mailer module configuration.
 *
 * SMTP is a default transport.
 */
$config['symfony_mailer.mailer_transport.smtp']['configuration']['user'] = $_ENV['SMTP_USERNAME'];
$config['symfony_mailer.mailer_transport.smtp']['configuration']['pass'] = $_ENV['SMTP_PASSWORD'];
$config['symfony_mailer.mailer_transport.smtp']['configuration']['host'] = $_ENV['SMTP_HOST'];
$config['symfony_mailer.mailer_transport.smtp']['configuration']['port'] = $_ENV['SMTP_PORT'];

/**
 * Shows a warning in toolbar that reminds about development version.
 */
$settings['niklan_development_warning'] = $_ENV['SHOW_DEVELOPMENT_WARNING'];
