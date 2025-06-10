<?php

use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\niklan\ExternalContent\Domain\MarkdownSource;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Parser\ArticleXmlParser;
use Drupal\niklan\ExternalContent\Pipeline\ArticleSyncPipeline;
use Drupal\niklan\ExternalContent\Stages\ArticleFinder;
use Drupal\niklan\ExternalContent\Validation\XmlValidator;
use Drupal\niklan\Plugin\ExternalContent\Environment\BlogArticle;

$pipeline = new ArticleSyncPipeline();
$pipeline->run(new SyncContext('private://content', \Drupal::logger('niklan.external_content')));
