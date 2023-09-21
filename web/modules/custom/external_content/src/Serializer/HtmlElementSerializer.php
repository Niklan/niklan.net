<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\HtmlElement;

/**
 * Provides a serializer for HTML element.
 */
final class HtmlElementSerializer implements NodeSerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function serialize(NodeInterface $node): Data {
    \assert($node instanceof HtmlElement);

    return new Data([
      'tag' => $node->getTag(),
      'attributes' => $node->getAttributes()->all(),
    ]);
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
    return $node instanceof HtmlElement;
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
  public function deserialize(Data $data, string $serialized_version): NodeInterface {
    $attributes = new Attributes($data->get('attributes'));

    return new HtmlElement($data->get('tag'), $attributes);
  }

  /**
   * {@selfdoc}
   */
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
