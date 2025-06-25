<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Paragraph;

use Drupal\external_content\Nodes\Content\Content;

final class Paragraph extends Content {

  public static function getType(): string {
    return 'paragraph';
  }

}
