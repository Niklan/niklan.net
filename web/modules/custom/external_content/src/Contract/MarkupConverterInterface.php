<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Provides a markup converter.
 */
interface MarkupConverterInterface {

  public function convert(ExternalContentHtml $result): ExternalContentHtml;

}
