<?php

use Drupal\Component\Utility\Timer;
use Drupal\niklan\Sync\ContentSyncManager;

Timer::start('q');
$queue = \Drupal::service('niklan.sync.content');
\assert($queue instanceof ContentSyncManager);
$queue->synchronize();
Timer::stop('q');
dump('Queue built for: ' . Timer::read('q') . 'ms' . PHP_EOL);
