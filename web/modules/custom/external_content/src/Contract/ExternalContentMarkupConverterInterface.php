<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;

interface ExternalContentMarkupConverterInterface {

  public function convert(ExternalContentFile $file): ExternalContentHtml;

}
