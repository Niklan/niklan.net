<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder\RenderArray;

use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;

/**
 * @template T of \Drupal\external_content\Nodes\Node
 */
interface Builder {

  /**
   * @phpstan-assert-if-true T $node
   */
  public function supports(Node $node): bool;

  /**
   * @param T $node
   */
  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray;

}
