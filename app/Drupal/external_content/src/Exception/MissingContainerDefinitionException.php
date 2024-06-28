<?php

declare(strict_types=1);

namespace Drupal\external_content\Exception;

/**
 * {@selfdoc}
 */
final class MissingContainerDefinitionException extends \RuntimeException {

  /**
   * {@selfdoc}
   */
  public function __construct(string $type, string $id) {
    $message = \sprintf(
      'The requested %s ID %s is not defined in the container. Did you forget to add a tag?',
      $type,
      $id,
    );
    parent::__construct($message);
  }

}
