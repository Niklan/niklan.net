<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Converter;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Represents an external content markup converter.
 */
interface ExternalContentMarkupConverterInterface extends EnvironmentAwareInterface {

  /**
   * Converts an external content markup into HTML.
   *
   * @param \Drupal\external_content\Data\ExternalContentFile $file
   *   The external content file.
   *
   * @return \Drupal\external_content\Data\ExternalContentHtml
   *   The external content HTML.
   */
  public function convert(ExternalContentFile $file): ExternalContentHtml;

}
