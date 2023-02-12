<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Builder;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a default plugin manager for grouper plugins.
 */
final class BuilderPluginManager extends DefaultPluginManager implements BuilderPluginManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/ExternalContent/Builder',
      $namespaces,
      $module_handler,
      '\Drupal\external_content\Plugin\ExternalContent\Builder\BuilderInterface',
      '\Drupal\external_content\Annotation\ExternalContentBuilder',
    );

    $this->alterInfo('external_content_builder_info');
    $this->setCacheBackend($cache_backend, 'external_content_builder_plugins');
  }

}
