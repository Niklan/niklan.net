<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes;

final class Document extends Node {

  #[\Override]
  public function setParent(Node $node): never {
    throw new \LogicException('Document node cannot have a parent.');
  }

  #[\Override]
  public function hasParent(): false {
    return FALSE;
  }

  public static function getNodeType(): string {
    return 'document';
  }

}
