<?php

declare(strict_types=1);

namespace Drupal\external_content\Exception;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;

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
    $available_serializer = \array_map(
      static fn (SerializerInterface $serializer): string => $serializer::class,
      \iterator_to_array($this->environment->getSerializers()),
    );

    $message = \sprintf(
      "Environment used for deserialization doesn't provides serializer for %s type of %s version. Available serializers: %s",
      $this->type,
      $this->version,
      \implode(', ', $available_serializer),
    );
    parent::__construct($message);
  }

}
