<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Exception\MissingDeserializerException;
use Drupal\external_content\Exception\MissingSerializerException;

/**
 * Provides a serializer for external content.
 */
final class Serializer implements SerializerInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function normalize(NodeInterface $document): string {
    return \json_encode($this->normalizeRecursive($document));
  }

  /**
   * {@selfdoc}
   */
  private function normalizeRecursive(NodeInterface $node): array {
    $children = [];

    foreach ($node->getChildren() as $child) {
      $children[] = $this->normalizeRecursive($child);
    }

    return $this->normalizeNode($node, $children);
  }

  /**
   * {@selfdoc}
   */
  private function normalizeNode(NodeInterface $node, array $children): array {
    foreach ($this->environment->getSerializers() as $serializer) {
      \assert($serializer instanceof NodeSerializerInterface);

      if (!$serializer->supportsSerialization($node)) {
        continue;
      }

      return [
        'type' => $serializer->getSerializationBlockType(),
        'version' => $serializer->getSerializerVersion(),
        'data' => $serializer->normalize($node)->all(),
        'children' => $children,
      ];
    }

    throw new MissingSerializerException($node, $this->environment);
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(string $json): NodeInterface {
    $json_array = \json_decode($json, TRUE);
    $node = $this->deserializeRecursive($json_array);
    \assert($node instanceof NodeInterface);

    return $node;
  }

  /**
   * {@selfdoc}
   */
  private function deserializeRecursive(array $json): NodeInterface {
    $element = $this->deserializeNode($json);

    foreach ($json['children'] as $child) {
      $element->addChild($this->deserializeRecursive($child));
    }

    return $element;
  }

  /**
   * {@selfdoc}
   */
  private function deserializeNode(array $node_data): NodeInterface {
    $version = $node_data['version'] ?? '0.0.0';

    $data = new Data($node_data['data']);

    foreach ($this->environment->getSerializers() as $serializer) {
      \assert($serializer instanceof NodeSerializerInterface);

      if (!$serializer->supportsDeserialization($node_data['type'], $version)) {
        continue;
      }

      return $serializer->deserialize($data, $version);
    }

    throw new MissingDeserializerException(
      $node_data['type'],
      $version,
      $this->environment,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
