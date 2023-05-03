<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Source;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\external_content\Contract\SourcePluginManagerInterface;

/**
 * Provides a default plugin manager for source content providers.
 *
 * Source - is a provider of content for processing.
 */
final class SourcePluginManager extends DefaultPluginManager implements SourcePluginManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/ExternalContent/Source',
      $namespaces,
      $module_handler,
      '\Drupal\external_content\Contract\SourcePluginInterface',
      '\Drupal\external_content\Annotation\ExternalContentSource',
    );

    $this->alterInfo('external_content_source_info');
    $this->setCacheBackend($cache_backend, 'external_content_source_plugins');
  }

}
