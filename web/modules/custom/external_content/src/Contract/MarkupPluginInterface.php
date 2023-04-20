<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

/**
 * Provides an interface for external content markup plugins.
 */
interface MarkupPluginInterface {

  /**
   * The default markup plugin weight.
   */
  public const DEFAULT_WEIGHT = -100;

  /**
   * Perform processing of source content to convert it into HTML.
   *
   * @param string $content
   *   The source content.
   *
   * @return string
   *   The content HTML result.
   */
  public function convert(string $content): string;

}
