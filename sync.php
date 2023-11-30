<?php

use Drupal\Component\Utility\Timer;
use Drupal\niklan\Sync\BlogSyncManager;

Timer::start('q');
$content_sync = \Drupal::service(BlogSyncManager::class);
\assert($content_sync instanceof BlogSyncManager);
$content_sync->synchronize('private://content');
Timer::stop('q');
dump('Queue built for: ' . Timer::read('q') . 'ms' . PHP_EOL);
