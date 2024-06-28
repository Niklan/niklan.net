<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Serializer;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\Data;

/**
 * Represents serializable node interface.
 */
interface SerializerInterface {

  /**
   * {@selfdoc}
   */
  public function normalize(NodeInterface $node, ChildSerializerInterface $child_serializer): array;

  /**
   * {@selfdoc}
   */
  public function getSerializerVersion(): string;

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
  public function supportsDeserialization(string $block_type, string $serialized_version): bool;

  /**
   * {@selfdoc}
   */
  public function deserialize(Data $data, string $stored_version, ChildSerializerInterface $child_serializer): NodeInterface;

}
