<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ListItem;

use Drupal\external_content\Nodes\Content\Content;

final class ListItem extends Content {

  public static function getType(): string {
    return 'list_item';
  }

}
