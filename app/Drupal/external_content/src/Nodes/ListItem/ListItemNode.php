<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ListItem;

use Drupal\external_content\Nodes\ContentNode;

final class ListItemNode extends ContentNode {

  public static function getType(): string {
    return 'list_item';
  }

}
