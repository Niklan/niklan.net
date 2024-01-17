<?php

// Import core settings, like 'entity_update_batch_size'. This file must be
// presented and scaffolded from core without any changes. It allows to have all
// the changes, additions and removals from core up to date on project.
include __DIR__ . '/default.settings.php';

$settings['config_sync_directory'] = '../config/sync';
$settings['file_private_path'] = '../private';
$settings['skip_permissions_hardening'] = TRUE;

$settings['database_cache_max_rows']['default'] = 10_000;
$settings['database_cache_max_rows']['bins']['config'] = 100_000;
$settings['database_cache_max_rows']['bins']['entity'] = 100_000;
$settings['database_cache_max_rows']['bins']['page'] = 50_000;
$settings['database_cache_max_rows']['bins']['dynamic_page_cache'] = 100_000;
$settings['database_cache_max_rows']['bins']['render'] = 100_000;

include __DIR__ . '/local.settings.php';
