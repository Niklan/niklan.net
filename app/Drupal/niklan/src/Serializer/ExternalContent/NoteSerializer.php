<?php declare(strict_types = 1);

namespace Drupal\niklan\Serializer\ExternalContent;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\niklan\Node\ExternalContent\Note;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class NoteSerializer implements SerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function normalize(NodeInterface $node): Data {
    \assert($node instanceof Note);

    return new Data([
      'type' => $node->type,
      'heading' => $node->heading,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof Note;
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
    return 'niklan:note';
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(Data $data, string $serialized_version): NodeInterface {
    return new Note($data->get('type'), $data->get('heading'));
  }

  /**
   * {@selfdoc}
   */
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
