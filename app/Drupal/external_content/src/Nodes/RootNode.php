<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes;

final class RootNode extends ContentNode {

  /**
   * @throws \LogicException
   */
  #[\Override]
  public function setParent(ContentNode $node): void {
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
