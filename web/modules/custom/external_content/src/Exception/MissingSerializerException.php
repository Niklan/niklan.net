<?php declare(strict_types = 1);

namespace Drupal\external_content\Exception;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;

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
    $message = \sprintf(
      "Environment used for serialization doesn't provides serializer for %s node.",
      \get_class($this->node),
    );
    parent::__construct($message);
  }

}
