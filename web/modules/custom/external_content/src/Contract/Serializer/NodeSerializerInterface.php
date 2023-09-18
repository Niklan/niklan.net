<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\Data;

/**
 * Represents serializable node interface.
 */
interface NodeSerializerInterface {

  /**
   * {@selfdoc}
   */
  public function serialize(NodeInterface $node): Data;

  /**
   * {@selfdoc}
   */
  public function getSerializationBlockType(): string;

  /**
   * {@selfdoc}
   */
  public function supportsSerialization(NodeInterface $node): bool;

  /**
   * {@selfdoc}
   */
  public function supportsDeserialization(string $block_type): bool;

  /**
   * {@selfdoc}
   */
  public function deserialize(Data $data): NodeInterface;

}
