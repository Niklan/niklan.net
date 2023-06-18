<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentHtml;


interface MarkupConverterPreprocessorInterface {

  /**
   *
   */
  public function preprocess(ExternalContentHtml $result): ExternalContentHtml;

}
