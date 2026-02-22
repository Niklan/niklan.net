<?php

declare(strict_types=1);

$databases['default']['default'] = [
  'database' => getenv('MARIADB_DATABASE') ?: 'drupal',
  'username' => getenv('MARIADB_USERNAME') ?: 'drupal',
  'password' => getenv('MARIADB_PASSWORD') ?: 'drupal',
  'host' => getenv('MARIADB_HOST') ?: 'mariadb',
  'prefix' => '',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
  'init_commands' => [
    // @see https://www.drupal.org/docs/system-requirements/setting-the-mysql-transaction-isolation-level
    'isolation_level' => 'SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED',
  ],
];

$settings['hash_salt'] = 'localhost';

$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

// $config['system.logging']['error_level'] = 'verbose';
// $config['system.performance']['css']['preprocess'] = FALSE;
// $config['system.performance']['js']['preprocess'] = FALSE;
// $settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';
// $settings['cache']['bins']['render'] = 'cache.backend.null';
// $settings['cache']['bins']['page'] = 'cache.backend.null';
// $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['config_exclude_modules'] = [
  'devel',
  'field_ui',
  'views_ui',
  'app_dev',
];

$config['system.mail']['interface'] = [ 'default' => 'symfony_mailer' ];
$config['system.mail']['mailer_dsn'] = [
  'scheme' => 'smtp',
  'host' => getenv('SMTP_HOST'),
  'port' => getenv('SMTP_PORT'),
  'user' => getenv('SMTP_USER'),
  'password' => getenv('SMTP_PASSWORD'),
];

$settings['trusted_host_patterns'][] = 'nginx';
$settings['trusted_host_patterns'][] = '^niklan\.localhost$';

$settings['external_content_directory'] = 'private://content';
$settings['external_content_repository_url'] = 'https://example.com/username/repository';
$settings['website_repository_url'] = 'https://example.com/username/repository';

$settings['telegram_token'] = NULL;
$settings['telegram_chat_id'] = NULL;
$settings['telegram_secret_token'] = NULL;

$config['cache_pilot.settings']['connection_dsn'] = 'tcp://php:9000';
