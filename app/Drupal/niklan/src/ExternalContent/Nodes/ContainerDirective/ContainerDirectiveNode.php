<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\ContainerDirective;

use Drupal\external_content\Nodes\ContentNode;

/**
 * A generic container directive node.
 */
final class ContainerDirectiveNode extends ContentNode {

  public function __construct(
    public readonly string $directiveType,
  ) {
    parent::__construct();
  }

  public static function getType(): string {
    return 'niklan:container_directive';
  }

}
