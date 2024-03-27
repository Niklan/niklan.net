<?php

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;

Timer::start('test');
$external_content = \Drupal::service(ExternalContentManagerInterface::class);
$environment = $external_content->getEnvironmentManager()->get('blog');

$source_collection = $external_content->getFinderManager()->find($environment);
$identified_source_collection = $external_content->getIdentifiersManager()->identify($source_collection, $environment);
$bundled_sources = $external_content->getBundlerManager()->bundle($identified_source_collection, $environment);
$bundle = $bundled_sources->bundles()[0];

$identified_source = $bundle->sources()[0];
$source = $identified_source->source;
$html = $external_content->getConverterManager()->convert($source, $environment);

$content = $external_content->getHtmlParserManager()->parse($html, $environment);

// check serializer
$json = $external_content->getSerializerManager()->normalize($content, $environment);
$content = $external_content->getSerializerManager()->deserialize($json, $environment);

$loader_results = $external_content->getLoaderManager()->load($bundle, $environment);
dump($loader_results);
