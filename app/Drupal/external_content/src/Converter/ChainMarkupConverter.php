<?php declare(strict_types = 1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupInterface;
use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupPluginManagerInterface;

/**
 * Provides converter from raw content markup into HTML.
 */
final class ChainMarkupConverter {

  /**
   * Constructs a new ChainMarkupConverter object.
   *
   * @param \Drupal\external_content\Plugin\ExternalContent\Markup\MarkupPluginManagerInterface $markupPluginManager
   *   The markup plugin manager.
   */
  public function __construct(
    protected MarkupPluginManagerInterface $markupPluginManager,
  ) {}

  /**
   * Converts raw content to HTML.
   *
   * @param string $identifier
   *   The content markup identifier (extension).
   * @param string $raw_content
   *   The raw content.
   *
   * @return string
   *   The converted into HTML markup. If no suitable converter is found, the
   *   raw content is returned.
   */
  public function convert(string $identifier, string $raw_content): string {
    $result = NULL;

    foreach ($this->markupPluginManager->getDefinitions() as $plugin_id => $definition) {
      if (!\in_array($identifier, $definition['markup_identifiers'])) {
        continue;
      }

      $plugin = $this->markupPluginManager->createInstance($plugin_id);
      \assert($plugin instanceof MarkupInterface);
      $result = $plugin->convert($raw_content);
    }

    return $result ?? $raw_content;
  }

}
