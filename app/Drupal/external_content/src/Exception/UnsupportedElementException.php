<?php

declare(strict_types=1);

namespace Drupal\external_content\Exception;

use Drupal\external_content\Contract\Exception\ElementProcessingException;

final class UnsupportedElementException extends \RuntimeException implements ElementProcessingException {

  public function __construct(
    string $processor,
    string $element,
  ) {
    parent::__construct(\sprintf('Element "%s" is not supported by processor "%s".', $element, $processor));
  }

}
