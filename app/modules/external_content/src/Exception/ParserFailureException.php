<?php

declare(strict_types=1);

namespace Drupal\external_content\Exception;

use Drupal\external_content\Contract\Exception\ElementProcessingException;

final class ParserFailureException extends \RuntimeException implements ElementProcessingException {

  public function __construct(
    string $processor,
    string $reason,
  ) {
    parent::__construct(\sprintf('Parser %s failed: %s', $processor, $reason));
  }

}
