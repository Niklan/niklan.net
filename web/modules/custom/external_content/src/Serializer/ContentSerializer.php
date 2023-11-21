<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\File;

/**
 * Provides serializer for the main document node.
 */
final class ContentSerializer implements NodeSerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function normalize(NodeInterface $node): Data {
    \assert($node instanceof Content);

    return new Data([
      'data' => $node->getData()->all(),
      'source' => [
        'id' => $node->getSource()->id(),
        'type' => $node->getSource()->type(),
        'data' => $node->getSource()->data()->all(),
      ],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof Content;
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
  public function getSerializationBlockType(): string {
    return 'external_content:document';
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(Data $data, string $serialized_version): NodeInterface {
    $file_info = $data->get('file');
    $file_data = new Data($file_info['data']);
    $file = new File(
      $file_info['working_dir'],
      $file_info['pathname'],
      'html',
      $file_data,
    );

    return new Content($file);
  }

  /**
   * {@inheritdoc}
   */
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
