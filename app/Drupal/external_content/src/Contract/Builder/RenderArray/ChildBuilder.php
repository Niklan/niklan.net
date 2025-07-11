<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder\RenderArray;

use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;

interface ChildBuilder {

  public function buildChildren(Node $parent_node, RenderArray $render_array): void;

}
