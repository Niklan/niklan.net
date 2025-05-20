<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Environment;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\external_content\Contract\Plugin\EnvironmentPlugin;

final class EnvironmentPluginManager extends DefaultPluginManager {

  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
  ) {
    parent::__construct(
      subdir: 'Plugin/ExternalContent/Environment',
      namespaces: $namespaces,
      module_handler: $module_handler,
      plugin_interface: EnvironmentPlugin::class,
      plugin_definition_attribute_name: Environment::class,
    );
    $this->alterInfo('external_content_environment_plugins');
    $this->setCacheBackend($cache_backend, 'external_content_environment_plugins');
  }

}
