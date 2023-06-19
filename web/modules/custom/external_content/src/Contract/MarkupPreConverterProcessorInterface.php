<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Represents an external content markup converter preprocessor.
 *
 * @todo
 */
interface MarkupPreConverterProcessorInterface {

  /**
   * Process an external content before main markup convert is started.
   *
   * @param \Drupal\external_content\Data\ExternalContentHtml $result
   *   The external content.
   *
   * @return \Drupal\external_content\Data\ExternalContentHtml
   *   The external content.
   */
  public function preprocess(ExternalContentHtml $result): ExternalContentHtml;

}
