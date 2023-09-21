<?php declare(strict_types = 1);

namespace Drupal\external_content\Exception;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;

/**
 * Provides an exception for missing deserializer.
 */
final class MissingDeserializerException extends \LogicException {

  /**
   * Constructs a new MissingDeserializerException instance.
   */
  public function __construct(
    public readonly string $type,
    public readonly string $version,
    public readonly EnvironmentInterface $environment,
  ) {
    $message = \sprintf(
      "Environment used for deserialization doesn't provides serializer for %s type of %s version. Available serializers: %s",
      $this->type,
      $this->version,
      \implode(', ', $this->environment->getSerializers()),
    );
    parent::__construct($message);
  }

}
