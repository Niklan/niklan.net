<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Converter;

use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Represents an external content markup converter preprocessor.
 *
 * @todo
 */
interface MarkupPreConverterInterface {

  /**
   * Process an external content before main markup convert is started.
   *
   * @param \Drupal\external_content\Data\ExternalContentHtml $result
   *   The external content.
   */
  public function preConvert(ExternalContentHtml $result): void;

}
