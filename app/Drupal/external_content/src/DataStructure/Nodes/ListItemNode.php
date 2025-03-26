<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

final class ListItemNode extends ElementNode {

  public static function getType(): string {
    return 'list_item';
  }

}
