<?php

use Drupal\external_content\Contract\Importer\Array\Parser\LiteralArrayParser;
use Drupal\external_content\Exporter\Array\ArrayExporter;
use Drupal\external_content\Exporter\Array\ArrayExporterContext;
use Drupal\external_content\Exporter\Array\ArrayExportRequest;
use Drupal\external_content\Exporter\Array\Builder\ArrayBuilder;
use Drupal\external_content\Exporter\Array\Builder\CodeNodeBuilder;
use Drupal\external_content\Exporter\Array\Builder\ContentNodeBuilder;
use Drupal\external_content\Exporter\Array\Builder\ElementNodeBuilder;
use Drupal\external_content\Exporter\Array\Builder\LiteralNodeBuilder;
use Drupal\external_content\Exporter\Array\Builder\TextNodeBuilder;
use Drupal\external_content\Importer\Array\ArrayImporter;
use Drupal\external_content\Importer\Array\ArrayImportRequest;
use Drupal\external_content\Importer\Array\Parser\ArrayParser;
use Drupal\external_content\Importer\Array\Parser\ArrayParseRequest;
use Drupal\external_content\Importer\Array\Parser\ElementArrayParser;
use Drupal\external_content\Importer\Html\ArrayImporterContext;
use Drupal\external_content\Importer\Html\ArrayImporterSource;
use Drupal\external_content\Importer\Html\HtmlImporterSource;
use Drupal\external_content\Importer\Html\HtmlImportRequest;
use Drupal\external_content\Importer\Html\Parser\HtmlParser;
use Drupal\external_content\Importer\Html\Parser\CodeParser;
use Drupal\external_content\Importer\Html\Parser\FormatParser;
use Drupal\external_content\Importer\Html\Parser\HeadingParser;
use Drupal\external_content\Importer\Html\HtmlImporter;
use Drupal\external_content\Importer\Html\HtmlImporterContext;
use Drupal\external_content\Importer\Html\Parser\ImageParser;
use Drupal\external_content\Importer\Html\Parser\LinkParser;
use Drupal\external_content\Importer\Html\Parser\ListParser;
use Drupal\external_content\Importer\Html\Parser\ListItemParser;
use Drupal\external_content\Importer\Html\Parser\ParagraphParser;
use Drupal\external_content\Importer\Html\Parser\TextParser;
use Drupal\external_content\Importer\Html\Parser\ThematicBreakParser;
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

// Array exporter.
$array_structure_builder = new ArrayBuilder();
$array_structure_builder->addBuilder(new ContentNodeBuilder(), -100);
$array_structure_builder->addBuilder(new ElementNodeBuilder(), -90);
$array_structure_builder->addBuilder(new LiteralNodeBuilder());

$array_export_request = new ArrayExportRequest($root_node, new ArrayExporterContext($logger), $array_structure_builder);
$array_exporter = new ArrayExporter();
$array = $array_exporter->export($array_export_request);

$json = json_encode($array->toArray());
$array = json_decode($json, TRUE);

// Array importer.
$array_parser = new ArrayParser();
$array_parser->addParser(new ElementArrayParser());
$array_parser->addParser(new LiteralArrayParser());

$array_import_request = new ArrayImportRequest(
  new ArrayImporterSource($array),
  new ArrayImporterContext($logger),
  $array_parser,
);
$array_importer = new ArrayImporter();
$ast = $array_importer->import($array_import_request);
dump($ast);