<?php

declare(strict_types=1);

namespace Drupal\external_content\Exception;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;

/**
 * Provides an exception for missing serializer.
 */
final class MissingSerializerException extends \LogicException {

  /**
   * Constructs a new MissingSerializerException instance.
   */
  public function __construct(
    public readonly NodeInterface $node,
    public readonly EnvironmentInterface $environment,
  ) {
    $available_serializer = \array_map(
      static fn (SerializerInterface $serializer): string => $serializer::class,
      \iterator_to_array($this->environment->getSerializers()),
    );

    $message = \sprintf(
      "Environment used for serialization doesn't provides serializer for %s node. Available serializers: %s",
      \get_class($this->node),
      \implode(', ', $available_serializer),
    );
    parent::__construct($message);
  }

}
