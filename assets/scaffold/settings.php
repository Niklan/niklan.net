<?php

// Import core settings, like 'entity_update_batch_size'. This file must be
// presented and scaffolded from core without any changes. It allows to have all
// the changes, additions and removals from core up to date on project.
include __DIR__ . '/default.settings.php';

$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/monolog.services.yml';

$settings['config_sync_directory'] = '../config/sync';
$settings['file_private_path'] = '../var/files/private';
$config['locale.settings']['translation']['path'] = '../var/files/private/translations';
$settings['file_temp_path'] = '../var/files/temporary';
$settings['skip_permissions_hardening'] = TRUE;

$settings['database_cache_max_rows']['default'] = 100_000;

include DRUPAL_ROOT . '/../local/settings.php';
