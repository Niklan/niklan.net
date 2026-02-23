<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\Figcaption;

use Drupal\external_content\Nodes\Node;

final class Figcaption extends Node {

  public static function getNodeType(): string {
    return 'niklan:figcaption';
  }

}
