<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\niklan\ExternalContent\Node\DrupalMedia;

/**
 * @ingroup content_sync
 */
final class DrupalMediaSerializer implements SerializerInterface {

  #[\Override]
  public function normalize(NodeInterface $node, ChildSerializerInterface $child_serializer): array {
    \assert($node instanceof DrupalMedia);

    return [
      'type' => $node->type,
      'uuid' => $node->uuid,
      'data' => $node->data->all(),
    ];
  }

  #[\Override]
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof DrupalMedia;
  }

  #[\Override]
  public function supportsDeserialization(string $block_type, string $serialized_version): bool {
    return $block_type === $this->getSerializationBlockType();
  }

  #[\Override]
  public function getSerializationBlockType(): string {
    return 'niklan:drupal_media';
  }

  #[\Override]
  public function deserialize(Data $data, string $stored_version, ChildSerializerInterface $child_serializer): NodeInterface {
    return new DrupalMedia(
      $data->get('type'),
      $data->get('uuid'),
      new Data($data->get('data')),
    );
  }

  #[\Override]
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
