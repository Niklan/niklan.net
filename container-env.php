<?php

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Environment\EnvironmentManager;

Timer::start('test');
$external_content = \Drupal::service(ExternalContentManagerInterface::class);
$environment = $external_content->getEnvironmentManager()->get('blog');

$source_collection = $external_content->getFinderManager()->find($environment);
$identified_source_collection = $external_content->getIdentifiersManager()->identify($source_collection, $environment);
$bundled_sources = $external_content->getBundlerManager()->bundle($identified_source_collection, $environment);

Timer::stop('test');
dump(Timer::read('test'));
