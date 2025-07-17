<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes;

final class Document extends Node {

  /**
   * @throws \LogicException
   */
  #[\Override]
  public function setParent(Node $node): void {
    throw new \LogicException('Document node cannot have a parent.');
  }

  #[\Override]
  public function hasParent(): bool {
    return FALSE;
  }

  public static function getNodeType(): string {
    return 'document';
  }

}
