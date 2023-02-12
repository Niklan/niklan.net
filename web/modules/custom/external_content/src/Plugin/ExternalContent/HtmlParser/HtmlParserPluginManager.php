<?php

declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\HtmlParser;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a default plugin manager for HTML element parser plugins.
 *
 * The external content can be in a different formats and markups. Markup
 * plugins converts them into HTML, after that, HTML nodes must be converted to
 * special DTO objects with more semantic naming and helper methods for specific
 * values.
 */
final class HtmlParserPluginManager extends DefaultPluginManager implements HtmlParserPluginManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/ExternalContent/HtmlParser',
      $namespaces,
      $module_handler,
      '\Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserInterface',
      '\Drupal\external_content\Annotation\ExternalContentHtmlParser',
    );

    $this->alterInfo('external_content_html_parser_info');
    $this->setCacheBackend(
      $cache_backend,
      'external_content_html_parser_plugins',
    );
  }

}
