<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\DependencyInjection\EnvironmentAwareClassResolverInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
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
final class Serializer implements EnvironmentAwareInterface, SerializerInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * Constructs a new Serializer instance.
   */
  public function __construct(
    private readonly EnvironmentAwareClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function serialize(NodeInterface $document): string {
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
      $instance = $this->classResolver->getInstance(
        $serializer,
        NodeSerializerInterface::class,
        $this->getEnvironment(),
      );
      \assert($instance instanceof NodeSerializerInterface);

      if (!$instance->supportsSerialization($node)) {
        continue;
      }

      return [
        'type' => $instance->getSerializationBlockType(),
        'version' => $instance->getSerializerVersion(),
        'data' => $instance->serialize($node)->all(),
        'children' => $children,
      ];
    }

    throw new MissingSerializerException($node, $this->getEnvironment());
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

    foreach ($this->getEnvironment()->getSerializers() as $serializer) {
      $instance = $this->classResolver->getInstance(
        $serializer,
        NodeSerializerInterface::class,
        $this->getEnvironment(),
      );
      \assert($instance instanceof NodeSerializerInterface);

      if (!$instance->supportsDeserialization($node_data['type'], $version)) {
        continue;
      }

      return $instance->deserialize($data, $version);
    }

    throw new MissingDeserializerException(
      $node_data['type'],
      $version,
      $this->getEnvironment(),
    );
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
