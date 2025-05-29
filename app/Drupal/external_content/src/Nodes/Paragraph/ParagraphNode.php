<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Paragraph;

use Drupal\external_content\Nodes\ContentNode;

final class ParagraphNode extends ContentNode {

  public static function getType(): string {
    return 'paragraph';
  }

}
