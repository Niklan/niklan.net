<?php

use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;
use Drupal\niklan\ExternalContent\Infrastructure\ArticleXmlParser;
use Drupal\niklan\ExternalContent\Infrastructure\XmlValidator;
use Drupal\niklan\ExternalContent\Pipeline\BlogArticleProcessPipeline;
use Drupal\niklan\ExternalContent\Pipeline\BlogSyncPipeline;
use Drupal\niklan\ExternalContent\Stages\BlogArticleFinderStage;
use Drupal\niklan\ExternalContent\Stages\BlogArticleProcessStage;

$logger = \Drupal::logger('system');
$context = new BlogSyncContext('private://content/blog', $logger);
$pipeline = new BlogSyncPipeline();
$pipeline->addStage(new BlogArticleFinderStage(new ArticleXmlParser(new XmlValidator())));
$pipeline->addStage(new BlogArticleProcessStage(new BlogArticleProcessPipeline()));
$context = $pipeline->run($context);