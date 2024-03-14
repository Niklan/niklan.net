<?php declare(strict_types = 1);

namespace Drupal\niklan\Serializer\ExternalContent;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\niklan\Node\ExternalContent\DrupalMedia;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class DrupalMediaSerializer implements SerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function normalize(NodeInterface $node, ChildSerializerInterface $child_serializer): array {
    \assert($node instanceof DrupalMedia);

    return [
      'uuid' => $node->uuid,
      'alt' => $node->alt,
      'title' => $node->title,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof DrupalMedia;
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
    return 'niklan:drupal_media';
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(Data $data, string $serialized_version, ChildSerializerInterface $child_serializer): NodeInterface {
    return new DrupalMedia(
      $data->get('uuid'),
      $data->get('alt'),
      $data->get('title'),
    );
  }

  /**
   * {@selfdoc}
   */
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
