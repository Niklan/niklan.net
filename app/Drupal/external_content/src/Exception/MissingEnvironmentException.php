<?php declare(strict_types = 1);

namespace Drupal\external_content\Exception;

/**
 * {@selfdoc}
 */
final class MissingEnvironmentException extends \RuntimeException {

  /**
   * {@selfdoc}
   */
  public function __construct(string $environment_id) {
    $message = \sprintf('The requested environment ID %s is not defined in container.', $environment_id);
    parent::__construct($message);
  }

}
