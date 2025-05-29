<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Nodes\ContentNode;

final class CalloutBodyNode extends ContentNode {

  public static function getType(): string {
    return 'niklan:callout_body';
  }

}
