<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Converter;

use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Represents an external content markup converter postprocessor.
 *
 * @todo
 */
interface MarkupPostConverterInterface {

  /**
   * Process an external content after main markup convert is completed.
   *
   * @param \Drupal\external_content\Data\ExternalContentHtml $result
   *   The external content.
   */
  public function postConvert(ExternalContentHtml $result): void;

}
