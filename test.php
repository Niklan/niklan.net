<?php

use Drupal\external_content\Data\SourceConfiguration;
use Drupal\niklan\Queue\ContentSyncQueue;

$queue = \Drupal::service('niklan.queue.content_sync');
\assert($queue instanceof ContentSyncQueue);

$configuration = new SourceConfiguration('private://content', 'params', 'content');
//$queue->buildQueue($configuration);

dump($queue->getQueue()->claimItem());
