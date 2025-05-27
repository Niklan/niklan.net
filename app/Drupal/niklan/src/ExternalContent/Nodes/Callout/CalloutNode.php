<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\DataStructure\Nodes\ContentNode;

final class CalloutNode extends ContentNode {

  public function __construct(
    public string $type,
  ) {}

  public static function getType(): string {
    return 'niklan:callout';
  }

}
