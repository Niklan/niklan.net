<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentHtml;


interface MarkupConverterPostprocessorInterface {

  /**
   *
   */
  public function postprocess(ExternalContentHtml $result): ExternalContentHtml;

}
