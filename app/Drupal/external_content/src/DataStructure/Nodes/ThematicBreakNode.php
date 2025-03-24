<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

final class ThematicBreakNode extends ElementNode {

  public function __construct() {
    parent::__construct('thematic_break');
  }

}
