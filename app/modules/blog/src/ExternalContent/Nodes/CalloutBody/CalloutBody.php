<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\CalloutBody;

use Drupal\external_content\Nodes\Node;

final class CalloutBody extends Node {

  public static function getNodeType(): string {
    return 'niklan:callout_body';
  }

}
