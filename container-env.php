<?php

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Environment\EnvironmentManager;
use Drupal\external_content\Source\Html;
use League\CommonMark\MarkdownConverter;

Timer::start('test');
$external_content = \Drupal::service(ExternalContentManagerInterface::class);
$environment = $external_content->getEnvironmentManager()->get('blog');

$source_collection = $external_content->getFinderManager()->find($environment);
$identified_source_collection = $external_content->getIdentifiersManager()->identify($source_collection, $environment);
$bundled_sources = $external_content->getBundlerManager()->bundle($identified_source_collection, $environment);
$bundle = $bundled_sources->bundles()[0];

// check converter
$identified_source = $bundle->sources()[0];
$source = $identified_source->source;
$html = $external_content->getConverterManager()->convert($source, $environment);
dump($html);

// check parser

// check serializer

//$loader_results = $external_content->getLoaderManager()->load($bundle, $environment);
//dump($loader_results);
