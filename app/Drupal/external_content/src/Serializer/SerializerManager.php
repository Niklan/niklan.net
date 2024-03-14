<?php declare(strict_types=1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Drupal\external_content\Exception\MissingDeserializerException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a serializer for external content.
 */
final readonly class SerializerManager implements SerializerManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ContainerInterface $container,
    private ChildSerializerInterface $childSerializer,
    private array $serializers = [],
  ) {}

  /**
   * {@inheritdoc}
   */
  public function normalize(NodeInterface $node, EnvironmentInterface $environment): string {
    $this->childSerializer->setEnvironment($environment);

    return \json_encode(
      value: $this->childSerializer->normalize($node),
      // For UTF-8 content it reduces the total size in half.
      flags: JSON_UNESCAPED_UNICODE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(string $json, EnvironmentInterface $environment): NodeInterface {
    $json_array = \json_decode($json, TRUE);
    $node = $this->deserializeRecursive($json_array, $environment);
    \assert($node instanceof NodeInterface);

    return $node;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function get(string $serializer_id): SerializerInterface {
    if (!$this->has($serializer_id)) {
      throw new MissingContainerDefinitionException(
        type: 'serializer',
        id: $serializer_id,
      );
    }

    $service = $this->serializers[$serializer_id]['service'];

    return $this->container->get($service);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function has(string $serializer_id): bool {
    return \array_key_exists($serializer_id, $this->serializers);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function list(): array {
    return $this->serializers;
  }

  /**
   * {@selfdoc}
   */
  private function deserializeRecursive(array $json, EnvironmentInterface $environment): NodeInterface {
    $element = $this->deserializeNode($json, $environment);

    foreach ($json['children'] as $child) {
      $element->addChild($this->deserializeRecursive($child, $environment));
    }

    return $element;
  }

  /**
   * {@selfdoc}
   */
  private function deserializeNode(array $node_data, EnvironmentInterface $environment): NodeInterface {
    $version = $node_data['version'] ?? '0.0.0';

    $data = new Data($node_data['data']);

    foreach ($environment->getSerializers() as $serializer) {
      \assert($serializer instanceof SerializerInterface);

      if (!$serializer->supportsDeserialization($node_data['type'], $version)) {
        continue;
      }

      return $serializer->deserialize($data, $version);
    }

    throw new MissingDeserializerException(
      $node_data['type'],
      $version,
      $environment,
    );
  }

}
