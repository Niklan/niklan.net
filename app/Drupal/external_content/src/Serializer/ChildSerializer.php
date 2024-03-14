<?php

declare(strict_types=1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Exception\MissingSerializerException;

/**
 * {@selfdoc}
 */
final class ChildSerializer implements ChildSerializerInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function normalize(NodeInterface $node): array {
    foreach ($this->environment->getSerializers() as $serializer) {
      \assert($serializer instanceof SerializerInterface);

      if (!$serializer->supportsSerialization($node)) {
        continue;
      }

      return [
        'type' => $serializer->getSerializationBlockType(),
        'version' => $serializer->getSerializerVersion(),
        'data' => $serializer->normalize($node, $this),
      ];
    }

    throw new MissingSerializerException($node, $this->environment);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function deserialize(string $json): NodeInterface {
    // TODO: Implement deserialize() method.
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
