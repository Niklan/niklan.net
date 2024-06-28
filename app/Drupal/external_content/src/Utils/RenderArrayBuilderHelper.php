<?php

declare(strict_types=1);

namespace Drupal\external_content\Utils;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;

/**
 * {@selfdoc}
 */
final class RenderArrayBuilderHelper {

  /**
   * {@selfdoc}
   */
  public static function buildChildren(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    $children = [];

    foreach ($node->getChildren() as $child) {
      \assert($child instanceof NodeInterface);
      $result = $child_builder->build($child);

      if (!$result->isBuilt()) {
        continue;
      }

      $children[] = $result->result();
    }

    return RenderArrayBuilderResult::withRenderArray($children);
  }

}
