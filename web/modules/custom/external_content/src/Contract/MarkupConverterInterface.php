<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

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
   *
   * @return \Drupal\external_content\Data\ExternalContentHtml
   *   The converted result.
   */
  public function convert(ExternalContentHtml $result): ExternalContentHtml;

}
