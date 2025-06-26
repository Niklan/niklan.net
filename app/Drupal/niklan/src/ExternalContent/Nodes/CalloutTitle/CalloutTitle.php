<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CalloutTitle;

use Drupal\external_content\Nodes\Content\Content;

final class CalloutTitle extends Content {

  public static function getType(): string {
    return 'niklan:callout_title';
  }

}
