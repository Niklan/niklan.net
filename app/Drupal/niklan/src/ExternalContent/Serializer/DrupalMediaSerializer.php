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
    /**
     * @var array{
     *   type: non-empty-string,
     *   uuid: non-empty-string,
     *   data: array<mixed>,
     * } $values
     */
    $values = $data->all();

    return new DrupalMedia($values['type'], $values['uuid'], new Data($values['data']));
  }

  #[\Override]
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
