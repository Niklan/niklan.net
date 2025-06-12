<?php

use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleSyncPipeline;

$pipeline = new ArticleSyncPipeline();
$pipeline->run(new SyncContext('private://content', \Drupal::logger('niklan.external_content')));
