<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\PlainText;

/**
 * Provides a serialization for a plain text element.
 */
final class PlainTextSerializer implements NodeSerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function serialize(PlainText|NodeInterface $node): Data {
    return new Data([
      'text' => $node->getContent(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof PlainText;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsDeserialization(string $block_type): bool {
    return $block_type === $this->getSerializationBlockType();
  }

  /**
   * {@inheritdoc}
   */
  public function getSerializationBlockType(): string {
    return 'external_content:plain_text';
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(Data $data): NodeInterface {
    return new PlainText($data->get('text'));
  }

}
