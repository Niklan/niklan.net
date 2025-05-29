<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ThematicBreak;

use Drupal\external_content\Nodes\ContentNode;

final class ThematicBreakNode extends ContentNode {

  public static function getType(): string {
    return 'thematic_break';
  }

}
