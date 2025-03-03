<?php

use Drupal\niklan\ExternalContent\Domain\BlogSyncConfig;
use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;
use Drupal\niklan\ExternalContent\Infrastructure\ArticleParser;
use Drupal\niklan\ExternalContent\Pipeline\BlogSyncPipeline;
use Drupal\niklan\ExternalContent\Stages\BlogArticleFinderStage;
use Psr\Log\NullLogger;

$context = new BlogSyncContext('private://content/blog');
$logger = new NullLogger();
$pipeline = new BlogSyncPipeline($logger);
$pipeline->addStage(new BlogArticleFinderStage(new ArticleParser()));
$context = $pipeline->run($context);
dump($context);