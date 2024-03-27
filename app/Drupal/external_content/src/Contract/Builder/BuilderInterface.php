<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Represents a content builder.
 */
interface BuilderInterface {

  /**
   * {@selfdoc}
   */
  public function build(NodeInterface $node, string $type, array $context = []): BuilderResultInterface;

  /**
   * {@selfdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool;

}
