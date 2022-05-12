<?php

declare(strict_types=1);

namespace Drupal\niklan_external_content\Plugin\ExternalContent\Format;

/**
 * Provides an interface for external content format plugins.
 */
interface FormatInterface {

  /**
   * Perform processing of source content to convert it into HTML.
   *
   * @param string $content
   *   The source content.
   *
   * @return string
   *   The content HTML result.
   */
  public function process(string $content): string;

}
