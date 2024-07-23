<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Converter;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\ConverterResult;

interface ConverterInterface {

  public function convert(SourceInterface $input): ConverterResult;

}
