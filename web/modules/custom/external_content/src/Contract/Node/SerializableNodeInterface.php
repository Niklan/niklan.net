<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Node;

use Drupal\external_content\Data\Data;

/**
 * Represents serializable node interface.
 */
interface SerializableNodeInterface {

  /**
   * Serializes all node data into value object.
   */
  public function serialize(): Data;

  /**
   * Instantiates a node from serialized data.
   */
  public static function deserialize(Data $data): NodeInterface;

}
