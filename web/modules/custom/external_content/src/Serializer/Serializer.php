<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides a serializer for external content.
 */
final class Serializer implements EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  public const UNDEFINED = 'external_content:undefined';

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@selfdoc}
   */
  public function serialize(ExternalContentDocument $document): string {
    return \json_encode($this->serializeRecursive($document));
  }

  /**
   * {@selfdoc}
   */
  private function serializeRecursive(NodeInterface $node): array {
    $children = [];

    foreach ($node->getChildren() as $child) {
      $children[] = $this->serializeRecursive($child);
    }

    return $this->serializeNode($node, $children);
  }

  /**
   * {@selfdoc}
   */
  private function serializeNode(NodeInterface $node, array $children): array {
    foreach ($this->environment->getSerializers() as $serializer) {
      \assert($serializer instanceof NodeSerializerInterface);

      if (!$serializer->supportsSerialization($node)) {
        continue;
      }

      return [
        'type' => $serializer->getSerializationBlockType(),
        'data' => $serializer->serialize($node),
        'children' => $children,
      ];
    }

    return [
      'type' => self::UNDEFINED,
      'data' => [],
      'children' => $children,
    ];
  }

  /**
   * {@selfdoc}
   */
  public function deserialize(string $json): ExternalContentDocument {
    $json_array = \json_decode($json, TRUE);
    $document = $this->deserializeRecursive($json_array);
    \assert($document instanceof ExternalContentDocument);

    return $document;
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
  private function deserializeNode(array $node_data): ?NodeInterface {
    $block_type = $node_data['type'] ?? self::UNDEFINED;

    if ($block_type === self::UNDEFINED) {
      return NULL;
    }

    $data = new Data($node_data['data']);

    foreach ($this->getEnvironment()->getSerializers() as $serializer) {
      \assert($serializer instanceof NodeSerializerInterface);

      if (!$serializer->supportsDeserialization($block_type)) {
        continue;
      }

      return $serializer->deserialize($data);
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    return $this->environment;
  }

}
