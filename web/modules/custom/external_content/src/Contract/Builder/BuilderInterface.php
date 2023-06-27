<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Represents an external content render array builder.
 */
interface BuilderInterface {

  /**
   * Builds a single document node.
   */
  public function build(NodeInterface $node): BuilderResultInterface;

}
