<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Markup;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a default plugin manager for external content markup types.
 *
 * Markup - is a content of a file in a specific format. The External Content
 * Markup plugins should convert content from a specific content into HTML.
 */
final class MarkupPluginManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/ExternalContent/Markup',
      $namespaces,
      $module_handler,
      '\Drupal\external_content\Plugin\ExternalContent\Markup\MarkupInterface',
      '\Drupal\external_content\Annotation\ExternalContentMarkup'
    );

    $this->alterInfo('external_content_markup_info');
    $this->setCacheBackend($cache_backend, 'external_content_markup_plugins');
  }

}
