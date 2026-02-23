<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\CalloutTitle;

use Drupal\external_content\Nodes\Node;

final class CalloutTitle extends Node {

  public static function getNodeType(): string {
    return 'niklan:callout_title';
  }

}
