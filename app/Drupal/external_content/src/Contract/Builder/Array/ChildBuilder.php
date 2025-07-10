<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder\Array;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

interface ChildBuilder {

  public function buildChildren(Node $parent_node, ArrayElement $array): void;

}
