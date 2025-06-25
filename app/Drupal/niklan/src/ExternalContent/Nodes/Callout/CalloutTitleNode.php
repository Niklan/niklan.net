<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Nodes\Content\Content;

final class CalloutTitleNode extends Content {

  public static function getType(): string {
    return 'niklan:callout_title';
  }

}
