<?php

declare(strict_types=1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\PlainText;

/**
 * Provides a serialization for a plain text element.
 */
final class PlainTextSerializer implements SerializerInterface {

  #[\Override]
  public function normalize(NodeInterface $node, ChildSerializerInterface $child_serializer): array {
    \assert($node instanceof PlainText);

    return [
      'text' => $node->getLiteral(),
    ];
  }

  #[\Override]
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof PlainText;
  }

  #[\Override]
  public function supportsDeserialization(string $block_type, string $serialized_version): bool {
    return $block_type === $this->getSerializationBlockType();
  }

  #[\Override]
  public function getSerializationBlockType(): string {
    return 'external_content:plain_text';
  }

  #[\Override]
  public function deserialize(Data $data, string $stored_version, ChildSerializerInterface $child_serializer): NodeInterface {
    return new PlainText($data->get('text'));
  }

  #[\Override]
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
