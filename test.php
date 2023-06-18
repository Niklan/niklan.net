<?php


use Drupal\external_content\Converter\ExternalContentMarkupConverter;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Finder\ExternalContentFinder;
use Drupal\external_content\Finder\MarkdownFinder;

$configuration = new Configuration([
  'markdown_finder' => [
    'dirs' => 'fff',
  ],
]);
$environment = new Environment($configuration);
$environment->addFinder(new MarkdownFinder());

$finder = new ExternalContentFinder($environment);
$files = $finder->find();

$markup_converter = new ExternalContentMarkupConverter($environment);
foreach ($files as $file) {
  $result = $markup_converter->convert($file);
  dump($result);
}
