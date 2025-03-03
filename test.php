<?php

use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;
use Drupal\niklan\ExternalContent\Infrastructure\ArticleParser;
use Drupal\niklan\ExternalContent\Pipeline\BlogSyncPipeline;
use Drupal\niklan\ExternalContent\Stages\BlogArticleFinderStage;

$context = new BlogSyncContext('private://content/blog');
$pipeline = new BlogSyncPipeline();
$pipeline->addStage(new BlogArticleFinderStage(new ArticleParser()));
$context = $pipeline->run($context);
dump($context);