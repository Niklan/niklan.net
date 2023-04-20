<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Grouper;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\external_content\Contract\GrouperPluginManagerInterface;

/**
 * Provides a default plugin manager for grouper plugins.
 */
final class GrouperPluginManager extends DefaultPluginManager implements GrouperPluginManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/ExternalContent/Grouper',
      $namespaces,
      $module_handler,
      '\Drupal\external_content\Contract\GrouperPluginInterface',
      '\Drupal\external_content\Annotation\ExternalContentGrouper',
    );

    $this->alterInfo('external_content_grouper_info');
    $this->setCacheBackend($cache_backend, 'external_content_grouper_plugins');
  }

}
