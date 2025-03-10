<?php

use Drupal\external_content\Node\CodeNode;
use Drupal\external_content\Importer\Html\CodeParser;
use Drupal\external_content\Importer\Html\FormatParser;
use Drupal\external_content\Importer\Html\HeadingParser;
use Drupal\external_content\Importer\Html\HtmlImporter;
use Drupal\external_content\Importer\Html\HtmlImporterContext;
use Drupal\external_content\Importer\Html\ImageParser;
use Drupal\external_content\Importer\Html\LinkParser;
use Drupal\external_content\Importer\Html\ListParser;
use Drupal\external_content\Importer\Html\ListItemParser;
use Drupal\external_content\Importer\Html\ParagraphParser;
use Drupal\external_content\Importer\Html\TextParser;
use Drupal\external_content\Importer\Html\ThematicBreakParser;
use Drupal\niklan\ExternalContent\Transformer\RemoteVideoHtmlNodeParser;
use League\CommonMark\MarkdownConverter;

$logger = \Drupal::logger('system');

$markdown_converter = \Drupal::service(MarkdownConverter::class);
$html = $markdown_converter->convert(file_get_contents('private://content/blog/2021/09/29/drupal-warmer-2/article.ru.md'))->getContent();

$context = new HtmlImporterContext($html, $logger);
$html_transformer = new HtmlImporter();
$html_transformer->addTransformer(new RemoteVideoHtmlNodeParser());
$html_transformer->addTransformer(new TextParser());
$html_transformer->addTransformer(new ParagraphParser());
$html_transformer->addTransformer(new FormatParser());
$html_transformer->addTransformer(new HeadingParser());
$html_transformer->addTransformer(new ImageParser());
$html_transformer->addTransformer(new LinkParser());
$html_transformer->addTransformer(new ListParser());
$html_transformer->addTransformer(new ListItemParser());
$html_transformer->addTransformer(new CodeParser());
$html_transformer->addTransformer(new ThematicBreakParser());
$root_node = $html_transformer->import($context);

//dump($root_node);
ob_start();
var_dump($root_node);
$content = ob_get_contents();
ob_end_clean();
file_put_contents('../dump.txt', $content);
