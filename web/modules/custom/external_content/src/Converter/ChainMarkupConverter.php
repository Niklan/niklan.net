<?php

declare(strict_types=1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Dto\SourceFileContent;
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
   * @return \Drupal\external_content\Dto\SourceFileContent
   *   The converted into HTML markup. If no suitable converter is found, the
   *   raw content is returned.
   */
  public function convert(string $identifier, string $raw_content): SourceFileContent {
    $result = NULL;
    foreach ($this->markupPluginManager->getDefinitions() as $plugin_id => $definition) {
      if (!\in_array($identifier, $definition['markup_identifiers'])) {
        continue;
      }
      /** @var \Drupal\external_content\Plugin\ExternalContent\Markup\MarkupInterface $plugin */
      $plugin = $this->markupPluginManager->createInstance($plugin_id);
      $result = $plugin->convert($raw_content);
    }

    return $result ? new SourceFileContent($result) : new SourceFileContent($raw_content);
  }

}
