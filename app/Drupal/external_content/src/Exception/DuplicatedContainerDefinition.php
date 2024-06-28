<?php

declare(strict_types=1);

namespace Drupal\external_content\Exception;

/**
 * {@selfdoc}
 */
final class DuplicatedContainerDefinition extends \RuntimeException {

  /**
   * {@selfdoc}
   */
  public function __construct(string $type, string $id, string $existing_service, string $current_service) {
    $message = \sprintf(
      'The external content %s with ID %s is already declared by %s, but the service %s tried to use the same ID.',
      $type,
      $id,
      $existing_service,
      $current_service,
    );
    parent::__construct($message);
  }

}
