<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser\Array;

use Drupal\external_content\Nodes\Node;

interface ChildParser {

  /**
   * @param iterable<\Drupal\external_content\DataStructure\ArrayElement> $arrays
   */
  public function parseChildren(iterable $arrays, Node $parent_node): void;

}
