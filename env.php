<?php

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;
use Drupal\external_content\Parser\Parser;
use Drupal\external_content\Serializer\Serializer;

$plugin_manager = \Drupal::service(EnvironmentPluginManagerInterface::class);
\assert($plugin_manager instanceof EnvironmentPluginManagerInterface);

$environment_plugin = $plugin_manager->createInstance('blog');
\assert($environment_plugin instanceof EnvironmentPluginInterface);

$environment = $environment_plugin->getEnvironment();

$parser = \Drupal::service(Parser::class);
\assert($parser instanceof Parser);
$parser->setEnvironment($environment);

$source = new \Drupal\external_content\Source\File(
  workingDir: __DIR__,
  pathname: __DIR__ . '/private/content/blog/2021/09/29/drupal-warmer-2/index.ru.md',
  type: 'html',
);
Timer::start('parse');
$content = $parser->parse($source);
Timer::stop('parse');

dump('Parse time, ms: ' . Timer::read('parse'));
