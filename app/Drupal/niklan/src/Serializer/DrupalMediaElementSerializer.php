<?php declare(strict_types = 1);

namespace Drupal\niklan\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\NodeSerializerInterface;
use Drupal\external_content\Data\Data;
use Drupal\niklan\Node\DrupalMediaElement;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class DrupalMediaElementSerializer implements NodeSerializerInterface {

  /**
   * {@inheritdoc}
   */
  public function normalize(NodeInterface $node): Data {
    \assert($node instanceof DrupalMediaElement);

    return new Data([
      'uuid' => $node->uuid,
      'alt' => $node->alt,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsSerialization(NodeInterface $node): bool {
    return $node instanceof DrupalMediaElement;
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
    return 'niklan:drupal_media_element';
  }

  /**
   * {@inheritdoc}
   */
  public function deserialize(Data $data, string $serialized_version): NodeInterface {
    return new DrupalMediaElement(
      $data->get('uuid'),
      $data->get('alt'),
    );
  }

  /**
   * {@selfdoc}
   */
  public function getSerializerVersion(): string {
    return '1.0.0';
  }

}
