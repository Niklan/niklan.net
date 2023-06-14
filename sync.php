<?php

use Drupal\Component\Utility\Timer;
use Drupal\niklan\Manager\ContentSyncManager;

Timer::start('q');
$queue = \Drupal::service('niklan.manager.content_sync');
\assert($queue instanceof ContentSyncManager);
$queue->synchronize();
Timer::stop('q');
dump('Queue built for: ' . Timer::read('q') . 'ms' . PHP_EOL);
