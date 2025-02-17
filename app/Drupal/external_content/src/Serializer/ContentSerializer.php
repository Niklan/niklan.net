<?php

declare(strict_types=1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Content;

/**
 * Provides serializer for the main document node.
 */
final class ContentSerializer implements SerializerInterface {

  #[\Override]
  public function normalize(NodeInterface $node, ChildSerializerInterface $child_serializer): array {
    \assert($node instanceof Content);

    return [
      'source' => $node->getData()->all(),
      'children' => \array_map(
        static fn (NodeInterface $child) => $child_serializer->normalize($child),
        $node->getChildren()->getArrayCopy(),
      ),
    ];
  }

  #[\Override]
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof Content;
  }

  #[\Override]
  public function supportsDeserialization(string $block_type, string $serialized_version): bool {
    return $block_type === $this->getSerializationBlockType();
  }

  #[\Override]
  public function getSerializationBlockType(): string {
    return 'external_content:content';
  }

  #[\Override]
  public function deserialize(Data $data, string $stored_version, ChildSerializerInterface $child_serializer): NodeInterface {
    /**
     * @var array{
     *   source: array,
     *   children: array,
     * } $values
     */
    $values = $data->all();
    $content = new Content(new Data($values['source']));

    foreach ($values['children'] as $child) {
      $content->addChild($child_serializer->deserialize($child));
    }

    return $content;
  }

  #[\Override]
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
