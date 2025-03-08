<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

final class RootNode extends ContentNode {

  public function __construct() {
    parent::__construct('root');
  }

  #[\Override]
  public function setParent(ContentNode $node): void {
    throw new \LogicException('Root node can not have a parent.');
  }

  #[\Override]
  public function hasParent(): bool {
    return FALSE;
  }

}
