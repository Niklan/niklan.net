<?php

use Drupal\Component\Utility\Timer;
use Drupal\niklan\Sync\BlogContentSyncManager;

Timer::start('q');
$content_sync = \Drupal::service(BlogContentSyncManager::class);
\assert($content_sync instanceof BlogContentSyncManager);
$content_sync->synchronize('private://content');
Timer::stop('q');
dump('Queue built for: ' . Timer::read('q') . 'ms' . PHP_EOL);
