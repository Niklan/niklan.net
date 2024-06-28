<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;

/**
 * Represents a content builder.
 */
interface RenderArrayBuilderInterface {

  /**
   * {@selfdoc}
   */
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult;

  /**
   * {@selfdoc}
   */
  public function supportsBuild(NodeInterface $node): bool;

}
