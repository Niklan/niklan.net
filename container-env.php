<?php

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Environment\EnvironmentManager;
use Drupal\external_content\Source\Html;
use League\CommonMark\MarkdownConverter;

Timer::start('test');
$external_content = \Drupal::service(ExternalContentManagerInterface::class);
$environment = $external_content->getEnvironmentManager()->get('blog');

$source_collection = $external_content->getFinderManager()->find($environment);
$identified_source_collection = $external_content->getIdentifiersManager()->identify($source_collection, $environment);
$bundled_sources = $external_content->getBundlerManager()->bundle($identified_source_collection, $environment);

Timer::stop('test');
dump(Timer::read('test'));

$test = new Html(
  <<<'Markdown'
  ::: foo
    :: bar
  :::
  
  **strong**
  Markdown
);
dump($external_content->getConverterManager()->convert($test, $environment));
