<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser\Array;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

interface ChildParser {

  public function parseChildren(ArrayElement $parent_array, Node $content_node): void;

}
