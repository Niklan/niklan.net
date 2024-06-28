<?php

declare(strict_types=1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\ChildSerializerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Element;

/**
 * Provides a serializer for HTML element.
 */
final class ElementSerializer implements SerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function normalize(NodeInterface $node, ChildSerializerInterface $child_serializer): array {
    \assert($node instanceof Element);

    $result = ['tag' => $node->getTag()];

    if ($node->getAttributes()->all()) {
      $result['attributes'] = $node->getAttributes()->all();
    }

    $result['children'] = \array_map(
      static fn (NodeInterface $child) => $child_serializer->normalize($child),
      $node->getChildren()->getArrayCopy(),
    );

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getSerializationBlockType(): string {
    return 'external_content:html_element';
  }

  /**
   * {@inheritdoc}
   */
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof Element;
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
  public function deserialize(Data $data, string $stored_version, ChildSerializerInterface $child_serializer): NodeInterface {
    $attributes = new Attributes($data->get('attributes') ?? []);
    $element = new Element($data->get('tag'), $attributes);

    foreach ($data->get('children') as $child) {
      $element->addChild($child_serializer->deserialize($child));
    }

    return $element;
  }

  /**
   * {@selfdoc}
   */
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
