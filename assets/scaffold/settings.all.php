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
 * Adjust SMTP module settings.
 *
 * Configure it to something that can handle SMTP emails. Use Mailhog to
 * intercept emails.
 */
$config['smtp.settings']['smtp_host'] = $_ENV['SMTP_HOST'];
$config['smtp.settings']['smtp_port'] = $_ENV['SMTP_PORT'];
$config['smtp.settings']['smtp_protocol'] = 'standard';
$config['smtp.settings']['smtp_username'] = $_ENV['SMTP_USERNAME'];
$config['smtp.settings']['smtp_password'] = $_ENV['SMTP_PASSWORD'];
$config['smtp.settings']['smtp_from'] = 'example@example.com';
$config['smtp.settings']['smtp_fromname'] = 'John Doe';
