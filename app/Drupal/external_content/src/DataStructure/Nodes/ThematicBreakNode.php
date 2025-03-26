<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

final class ThematicBreakNode extends ElementNode {

  public static function getType(): string {
    return 'thematic_break';
  }

}
