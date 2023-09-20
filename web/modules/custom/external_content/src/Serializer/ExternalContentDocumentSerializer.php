<?php declare(strict_types = 1);

namespace Drupal\external_content\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides serializer for the main document node.
 */
final class ExternalContentDocumentSerializer implements NodeSerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function serialize(NodeInterface $node): Data {
    \assert($node instanceof ExternalContentDocument);

    return new Data([
      'file' => [
        'working_dir' => $node->getFile()->getWorkingDir(),
        'pathname' => $node->getFile()->getPathname(),
        'data' => $node->getFile()->getData()->all(),
      ],
    ]);
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
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof ExternalContentDocument;
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
  public function deserialize(Data $data): NodeInterface {
    $file_info = $data->get('file');
    $file_data = new Data($file_info['data']);
    $file = new ExternalContentFile(
      $file_info['working_dir'],
      $file_info['pathname'],
      $file_data,
    );

    return new ExternalContentDocument($file);
  }

}
