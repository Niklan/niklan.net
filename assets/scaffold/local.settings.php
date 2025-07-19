<?php
use Symfony\Component\ErrorHandler\Debug;

$error_handler = Debug::enable();
$error_handler->setExceptionHandler([$error_handler, 'renderException']);

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
  'niklan_dev',
];

$config['symfony_mailer.mailer_transport.smtp']['configuration']['user'] = 'example@example.com';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['pass'] = 'password';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['host'] = 'smtp.yandex.ru';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['port'] = 465;
$config['symfony_mailer.settings']['default_transport'] = 'sendmail';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['query']['command'] = getenv('PHP_SENDMAIL_PATH') . ' ' . getenv('SSMTP_MAILHUB');
$settings['mailer_sendmail_commands'] = [
  $config['symfony_mailer.mailer_transport.sendmail']['configuration']['query']['command'],
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
