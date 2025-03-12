<?php

use Drupal\external_content\Importer\Html\HtmlImporterSource;
use Drupal\external_content\Importer\Html\HtmlImportRequest;
use Drupal\external_content\Importer\Html\HtmlParser;
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
use Drupal\niklan\ExternalContent\Importer\Html\RemoteVideoParser;
use League\CommonMark\MarkdownConverter;

$logger = \Drupal::logger('system');

$markdown_converter = \Drupal::service(MarkdownConverter::class);
$html = $markdown_converter->convert(file_get_contents('private://content/blog/2021/09/29/drupal-warmer-2/article.ru.md'))->getContent();

$html_parser = new HtmlParser();
$html_parser->addParser(new RemoteVideoParser());
$html_parser->addParser(new TextParser());
$html_parser->addParser(new ParagraphParser());
$html_parser->addParser(new FormatParser());
$html_parser->addParser(new HeadingParser());
$html_parser->addParser(new ImageParser());
$html_parser->addParser(new LinkParser());
$html_parser->addParser(new ListParser());
$html_parser->addParser(new ListItemParser());
$html_parser->addParser(new CodeParser());
$html_parser->addParser(new ThematicBreakParser());

$request = new HtmlImportRequest(
  new HtmlImporterSource($html),
  new HtmlImporterContext($logger),
  $html_parser,
);
$html_importer = new HtmlImporter();
$root_node = $html_importer->import($request);

//dump($root_node);
ob_start();
var_dump($root_node);
$content = ob_get_contents();
ob_end_clean();
file_put_contents('../dump.txt', $content);
