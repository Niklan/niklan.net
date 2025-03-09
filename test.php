<?php

use Drupal\niklan\ExternalContent\Domain\BlogSyncPipelineContext;
use Drupal\niklan\ExternalContent\Parser\ArticleXmlParser;
use Drupal\niklan\ExternalContent\Stages\MarkdownToHtmlConverter;
use Drupal\niklan\ExternalContent\Validation\XmlValidator;
use Drupal\niklan\ExternalContent\Pipeline\BlogArticleProcessPipeline;
use Drupal\niklan\ExternalContent\Pipeline\BlogSyncPipeline;
use Drupal\niklan\ExternalContent\Stages\BlogArticleFinderPipelineStage;
use Drupal\niklan\ExternalContent\Stages\BlogArticleProcessPipelineStage;
use League\CommonMark\MarkdownConverter;

$logger = \Drupal::logger('system');

$context = new BlogSyncPipelineContext('private://content/blog', $logger);
$pipeline = new BlogSyncPipeline();
$pipeline->addStage(new BlogArticleFinderPipelineStage(new ArticleXmlParser(new XmlValidator())));

$markdown_converter = \Drupal::service(MarkdownConverter::class);

$blog_article_process_pipeline = new BlogArticleProcessPipeline();
// @todo Add BlogArticleParsePipeline
//   Add AST setter/getter for BlogArticleTranslation
$blog_article_process_pipeline->addStage(new MarkdownToHtmlConverter($markdown_converter));
$pipeline->addStage(new BlogArticleProcessPipelineStage($blog_article_process_pipeline));

$context = $pipeline->run($context);