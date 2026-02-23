<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Exception;

final class XmlValidationException extends \RuntimeException {

  /**
   * @param string $message
   * @param list<string> $errors
   */
  public function __construct(string $message, array $errors = []) {
    if (\count($errors) > 0) {
      $message = \sprintf('%s: %s', $message, \implode('\n', $errors));
    }

    parent::__construct($message);
  }

}
