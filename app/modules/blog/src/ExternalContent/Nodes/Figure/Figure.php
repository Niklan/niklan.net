<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\Figure;

use Drupal\external_content\Nodes\Node;

final class Figure extends Node {

  public static function getNodeType(): string {
    return 'niklan:figure';
  }

}
