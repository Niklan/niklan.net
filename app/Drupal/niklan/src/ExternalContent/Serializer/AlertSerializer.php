<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\niklan\ExternalContent\Node\Alert;

/**
 * @ingroup content_sync
 */
final class AlertSerializer implements SerializerInterface {

  #[\Override]
  public function normalize(NodeInterface $node, ChildSerializerInterface $child_serializer): array {
    \assert($node instanceof Alert);

    $data = [
      'type' => $node->type,
    ];

    if ($node->heading) {
      $data['heading'] = $child_serializer->normalize($node->heading);
    }

    $data['content'] = \array_map(
      static fn (NodeInterface $child) => $child_serializer->normalize($child),
      $node->getChildren()->getArrayCopy(),
    );

    return $data;
  }

  #[\Override]
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof Alert;
  }

  #[\Override]
  public function supportsDeserialization(string $block_type, string $serialized_version): bool {
    return $block_type === $this->getSerializationBlockType();
  }

  #[\Override]
  public function getSerializationBlockType(): string {
    return 'niklan:alert';
  }

  #[\Override]
  public function deserialize(Data $data, string $stored_version, ChildSerializerInterface $child_serializer): NodeInterface {
    $alert = new Alert(
      type: $data->get('type'),
      heading: $data->has('heading') ? $child_serializer->deserialize($data->get('heading')) : NULL,
    );

    foreach ($data->get('content') as $child) {
      $alert->addChild($child_serializer->deserialize($child));
    }

    return $alert;
  }

  #[\Override]
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
