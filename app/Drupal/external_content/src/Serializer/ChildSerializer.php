<?php

declare(strict_types=1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Exception\MissingDeserializerException;
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
  public function deserialize(array $element): NodeInterface {
    \assert($element['type'], 'Missing data type.');
    $version = $element['version'] ?? '0.0.0';
    $data = new Data($element['data'] ?? []);

    foreach ($this->environment->getSerializers() as $serializer) {
      \assert($serializer instanceof SerializerInterface);

      if (!$serializer->supportsDeserialization($element['type'], $version)) {
        continue;
      }

      return $serializer->deserialize($data, $version, $this);
    }

    throw new MissingDeserializerException(
      $element['type'],
      $version,
      $this->environment,
    );
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
