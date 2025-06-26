<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CalloutBody;

use Drupal\external_content\Nodes\Content\Content;

final class CalloutBody extends Content {

  public static function getType(): string {
    return 'niklan:callout_body';
  }

}
