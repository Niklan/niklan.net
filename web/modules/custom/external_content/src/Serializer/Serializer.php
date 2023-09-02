<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides a serializer for external content.
 */
final class Serializer {

  /**
   * {@selfdoc}
   */
  public function serialize(ExternalContentDocument $document): string {
    return \json_encode($this->serializeRecursive($document));
  }

  /**
   * {@selfdoc}
   */
  private function serializeRecursive(NodeInterface $node): array {
    $children = [];

    foreach ($node->getChildren() as $child) {
      $children[] = $this->serializeRecursive($child);
    }

    return [
      'type' => $node::class,
      'data' => $node->serialize()->all(),
      'children' => $children,
    ];
  }

  /**
   * {@selfdoc}
   */
  public function deserialize(string $json): ExternalContentDocument {
    $json_array = \json_decode($json, TRUE);
    $document = $this->deserializeRecursive($json_array);
    \assert($document instanceof ExternalContentDocument);

    return $document;
  }

  /**
   * {@selfdoc}
   */
  private function deserializeRecursive(array $json): NodeInterface {
    $node_class = $json['type'];
    $data = new Data($json['data']);
    // @todo Check for class_exists() and throw exception or stub result.
    $element = $node_class::deserialize($data);
    \assert($element instanceof NodeInterface);

    foreach ($json['children'] as $child) {
      $element->addChild($this->deserializeRecursive($child));
    }

    return $element;
  }

}
