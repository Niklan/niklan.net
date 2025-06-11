<?php

use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleSyncPipeline;

$pipeline = new ArticleSyncPipeline();
$pipeline->run(new SyncContext('private://content/blog/2019/05/09', \Drupal::logger('niklan.external_content')));
