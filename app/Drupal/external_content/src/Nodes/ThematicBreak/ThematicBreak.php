<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ThematicBreak;

use Drupal\external_content\Nodes\Content\Content;

final class ThematicBreak extends Content {

  public static function getType(): string {
    return 'thematic_break';
  }

}
