<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Node;

use Drupal\external_content\DataStructure\Nodes\ContentNode;

interface NodeVisitor {

  public function visit(ContentNode $node): void;

}
