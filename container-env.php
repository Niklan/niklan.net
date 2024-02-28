<?php

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Environment\EnvironmentManager;

Timer::start('test');
$environment = \Drupal::service(EnvironmentManager::class)->getEnvironment('blog');

$finder = \Drupal::service(FinderManagerInterface::class);
$finder->setEnvironment($environment);
$source_collection = $finder->find();

$identifier = \Drupal::service(IdentifierManagerInterface::class);
$identifier->setEnvironment($environment);
$identified_source_collection = $identifier->identify($source_collection);

$bundler = \Drupal::service(BundlerManagerInterface::class);
$bundler->setEnvironment($environment);
$bundled_sources = $bundler->bundle($identified_source_collection);

dump($bundled_sources);

Timer::stop('test');
dump(Timer::read('test'));
