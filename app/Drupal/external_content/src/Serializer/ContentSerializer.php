<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Content;

/**
 * Provides serializer for the main document node.
 */
final class ContentSerializer implements SerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function normalize(NodeInterface $node): Data {
    \assert($node instanceof Content);

    return $node->getData();
  }

  /**
   * {@inheritdoc}
   */
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof Content;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsDeserialization(string $block_type, string $serialized_version): bool {
    return $block_type === $this->getSerializationBlockType();
  }

  /**
   * {@inheritdoc}
   */
  public function getSerializationBlockType(): string {
    return 'external_content:content';
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(Data $data, string $serialized_version): NodeInterface {
    return new Content($data);
  }

  /**
   * {@inheritdoc}
   */
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
