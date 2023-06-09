<?php

use Drupal\external_content\Data\SourceConfiguration;
use Drupal\niklan\Manager\ContentSyncManager;
use Drupal\niklan\Queue\ContentSyncQueueManager;

$queue = \Drupal::service('niklan.manager.content_sync');
\assert($queue instanceof ContentSyncManager);

//$configuration = new SourceConfiguration('private://content', 'params', 'content');
//$queue->buildQueue($configuration);

dump($queue->synchronize());
