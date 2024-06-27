<?php

declare(strict_types=1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
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
      flags: \JSON_UNESCAPED_UNICODE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(string $json, EnvironmentInterface $environment): NodeInterface {
    $this->childSerializer->setEnvironment($environment);
    $json_array = \json_decode($json, TRUE);

    return $this->childSerializer->deserialize($json_array);
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

}
