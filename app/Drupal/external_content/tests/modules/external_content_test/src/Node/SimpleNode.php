<?php

declare(strict_types=1);

namespace Drupal\external_content_test\Node;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Node\Node;

/**
 * Provides a simple stub for node.
 */
final class SimpleNode extends Node {

  /**
   * {@inheritdoc}
   */
  public static function deserialize(Data $data): NodeInterface {
    return new self();
  }

  /**
   * {@inheritdoc}
   */
  public function serialize(): Data {
    return new Data([]);
  }

}
