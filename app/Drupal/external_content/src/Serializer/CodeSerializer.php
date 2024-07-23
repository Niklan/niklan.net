<?php

declare(strict_types=1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Code;

/**
 * Provides a serialization for <code> element.
 */
final class CodeSerializer implements SerializerInterface {

  #[\Override]
  public function normalize(NodeInterface $node, ChildSerializerInterface $child_serializer): array {
    \assert($node instanceof Code);

    return [
      'code' => $node->getLiteral(),
    ];
  }

  #[\Override]
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof Code;
  }

  #[\Override]
  public function supportsDeserialization(string $block_type, string $serialized_version): bool {
    return $block_type === $this->getSerializationBlockType();
  }

  #[\Override]
  public function getSerializationBlockType(): string {
    return 'external_content:code';
  }

  #[\Override]
  public function deserialize(Data $data, string $stored_version, ChildSerializerInterface $child_serializer): NodeInterface {
    return new Code($data->get('code'));
  }

  #[\Override]
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
