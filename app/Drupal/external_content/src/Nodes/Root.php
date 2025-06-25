<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes;

use Drupal\external_content\Nodes\Content\Content;

final class Root extends Content {

  /**
   * @throws \LogicException
   */
  #[\Override]
  public function setParent(Content $node): void {
    throw new \LogicException('Root node cannot have a parent.');
  }

  #[\Override]
  public function hasParent(): bool {
    return FALSE;
  }

  public static function getType(): string {
    return 'root';
  }

}
