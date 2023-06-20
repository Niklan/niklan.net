<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Converter;

use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Represents a specific external content markup converter.
 */
interface MarkupConverterInterface {

  /**
   * Converts a markup into HTML.
   *
   * @param \Drupal\external_content\Data\ExternalContentHtml $result
   *   The current content.
   */
  public function convert(ExternalContentHtml $result): void;

}
