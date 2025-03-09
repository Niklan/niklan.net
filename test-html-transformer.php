<?php

use Drupal\external_content\Transformer\Html\FormatHtmlNodeTransformer;
use Drupal\external_content\Transformer\Html\HeadingHtmlNodeTransformer;
use Drupal\external_content\Transformer\Html\HtmlTransformer;
use Drupal\external_content\Transformer\Html\HtmlTransformerContext;
use Drupal\external_content\Transformer\Html\ParagraphHtmlNodeTransformer;
use Drupal\external_content\Transformer\Html\TextHtmlNodeTransformer;
use Drupal\niklan\ExternalContent\Transformer\RemoteVideoHtmlNodeTransformer;
use League\CommonMark\MarkdownConverter;

$logger = \Drupal::logger('system');

$markdown_converter = \Drupal::service(MarkdownConverter::class);
$html = $markdown_converter->convert(file_get_contents('private://content/blog/2021/09/29/drupal-warmer-2/article.ru.md'))->getContent();

$context = new HtmlTransformerContext($html, $logger);
$html_transformer = new HtmlTransformer();
$html_transformer->addTransformer(new RemoteVideoHtmlNodeTransformer());
$html_transformer->addTransformer(new TextHtmlNodeTransformer());
$html_transformer->addTransformer(new ParagraphHtmlNodeTransformer());
$html_transformer->addTransformer(new FormatHtmlNodeTransformer());
$html_transformer->addTransformer(new HeadingHtmlNodeTransformer());
$root_node = $html_transformer->transform($context);

//dump($root_node);