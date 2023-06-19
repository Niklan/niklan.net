<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Represents an external content markup converter postprocessor.
 *
 * @todo
 */
interface MarkupPostConverterProcessorInterface {

  /**
   * Process an external content after main markup convert is completed.
   *
   * @param \Drupal\external_content\Data\ExternalContentHtml $result
   *   The external content.
   *
   * @return \Drupal\external_content\Data\ExternalContentHtml
   *   The external content.
   */
  public function postprocess(ExternalContentHtml $result): ExternalContentHtml;

}
