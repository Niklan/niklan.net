<?php

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\external_content\Data\SourceConfiguration;
use Drupal\niklan\Manager\ContentSyncManager;
use Drupal\niklan\Queue\ContentSyncQueueManager;

$queue = \Drupal::service('niklan.manager.content_sync');
\assert($queue instanceof ContentSyncManager);

//$configuration = new SourceConfiguration('private://content', 'params', 'content');
//$queue->buildQueue($configuration);

dump($queue->synchronize());

//$t = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, '2013-03-25T19:51:06');
//dump($t->getTimestamp());
