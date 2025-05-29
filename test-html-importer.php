<?php

use Drupal\external_content\Exporter\Array\ArrayBuilder;
use Drupal\external_content\Exporter\Array\ArrayExporter;
use Drupal\external_content\Exporter\Array\ArrayExporterContext;
use Drupal\external_content\Exporter\Array\ArrayExportRequest;
use Drupal\external_content\Exporter\Array\DefaultArrayBuilderExtension;
use Drupal\external_content\Importer\Array\ArrayImporter;
use Drupal\external_content\Importer\Array\ArrayImporterContext;
use Drupal\external_content\Importer\Array\ArrayImporterSource;
use Drupal\external_content\Importer\Array\ArrayImportRequest;
use Drupal\external_content\Importer\Array\ArrayParser;
use Drupal\external_content\Importer\Array\DefaultArrayParserExtension;
use Drupal\external_content\Importer\Html\DefaultHtmlParserExtension;
use Drupal\external_content\Importer\Html\HtmlImporter;
use Drupal\external_content\Importer\Html\HtmlImporterContext;
use Drupal\external_content\Importer\Html\HtmlImporterSource;
use Drupal\external_content\Importer\Html\HtmlImportRequest;
use Drupal\external_content\Importer\Html\HtmlParser;
use Drupal\external_content\Utils\Registry;
use League\CommonMark\MarkdownConverter;

$logger = \Drupal::logger('system');

$markdown_converter = \Drupal::service(MarkdownConverter::class);
$html = $markdown_converter->convert(file_get_contents('private://content/blog/2021/09/29/drupal-warmer-2/article.ru.md'))->getContent();

$html_parsers = new Registry();
$default_html_parser_extension = new DefaultHtmlParserExtension();
$default_html_parser_extension->register($html_parsers);
$html_parser = new HtmlParser($html_parsers);

$request = new HtmlImportRequest(
  new HtmlImporterSource($html),
  new HtmlImporterContext($logger),
  $html_parser,
);
$html_importer = new HtmlImporter();
$root_node = $html_importer->import($request);

// Array exporter.
$array_builders = new Registry();
$default_array_builder_extension = new DefaultArrayBuilderExtension();
$default_array_builder_extension->register($array_builders);
$array_builder = new ArrayBuilder($array_builders);

$array_export_request = new ArrayExportRequest($root_node, new ArrayExporterContext($logger), $array_builder);
$array_exporter = new ArrayExporter();
$array = $array_exporter->export($array_export_request);

$json = json_encode($array->toArray());
$array = json_decode($json, TRUE);

// Array importer.
$array_parsers = new Registry();
$default_array_parser_extension = new DefaultArrayParserExtension();
$default_array_parser_extension->register($array_parsers);
$array_parser = new ArrayParser($array_parsers);

$array_import_request = new ArrayImportRequest(
  new ArrayImporterSource($array),
  new ArrayImporterContext($logger),
  $array_parser,
);
$array_importer = new ArrayImporter();
$ast = $array_importer->import($array_import_request);