<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Loader;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a default plugin manager for loader plugins.
 */
final class LoaderPluginManager extends DefaultPluginManager implements LoaderPluginManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/ExternalContent/Loader',
      $namespaces,
      $module_handler,
      '\Drupal\external_content\Plugin\ExternalContent\Loader\LoaderInterface',
      '\Drupal\external_content\Annotation\ExternalContentLoader',
    );

    $this->alterInfo('external_content_loader_info');
    $this->setCacheBackend($cache_backend, 'external_content_loader_plugins');
  }

}
