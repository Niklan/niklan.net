<?php

use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;
use Drupal\niklan\ExternalContent\Infrastructure\ArticleParser;
use Drupal\niklan\ExternalContent\Infrastructure\XmlValidator;
use Drupal\niklan\ExternalContent\Pipeline\BlogSyncPipeline;
use Drupal\niklan\ExternalContent\Stages\BlogArticleFinderStage;

$logger = \Drupal::logger('system');
$context = new BlogSyncContext('private://content/blog', $logger);
$pipeline = new BlogSyncPipeline();
$pipeline->addStage(new BlogArticleFinderStage(new ArticleParser(new XmlValidator())));
$context = $pipeline->run($context);
//dump($context);