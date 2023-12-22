<?php declare(strict_types = 1);

namespace Drupal\external_content\Exception;

/**
 * Provides an exception for missing container.
 */
final class MissingContainerException extends \LogicException {

  /**
   * Constructs a new MissingContainerException instance.
   */
  public function __construct(
    public readonly string $containerAwareClass,
  ) {
    $message = \sprintf(
      "Environment has a container aware class %s, but service container is not provided for environment. Provide service container to environment or remove that class from it.",
      $this->containerAwareClass,
    );
    parent::__construct($message);
  }

}
