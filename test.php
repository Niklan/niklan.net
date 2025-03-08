<?php

use Drupal\niklan\ExternalContent\Domain\BlogSyncContext;
use Drupal\niklan\ExternalContent\Parser\ArticleXmlParser;
use Drupal\niklan\ExternalContent\Stages\MarkdownToHtmlConverter;
use Drupal\niklan\ExternalContent\Validation\XmlValidator;
use Drupal\niklan\ExternalContent\Pipeline\BlogArticleProcessPipeline;
use Drupal\niklan\ExternalContent\Pipeline\BlogSyncPipeline;
use Drupal\niklan\ExternalContent\Stages\BlogArticleFinderStage;
use Drupal\niklan\ExternalContent\Stages\BlogArticleProcessStage;
use League\CommonMark\MarkdownConverter;

$logger = \Drupal::logger('system');

$context = new BlogSyncContext('private://content/blog', $logger);
$pipeline = new BlogSyncPipeline();
$pipeline->addStage(new BlogArticleFinderStage(new ArticleXmlParser(new XmlValidator())));

$markdown_converter = \Drupal::service(MarkdownConverter::class);

$blog_article_process_pipeline = new BlogArticleProcessPipeline();
// @todo Add BlogArticleParsePipeline
//   Add AST setter/getter for BlogArticleTranslation
$blog_article_process_pipeline->addStage(new MarkdownToHtmlConverter($markdown_converter));
$pipeline->addStage(new BlogArticleProcessStage($blog_article_process_pipeline));

$context = $pipeline->run($context);