<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentHtml;


interface ExternalContentHtmlParserInterface {

  /**
   *
   */
  public function parse(ExternalContentHtml $html): ExternalContentDocument;

}
